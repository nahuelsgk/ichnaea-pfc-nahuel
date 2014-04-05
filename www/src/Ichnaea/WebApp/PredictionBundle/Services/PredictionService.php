<?php 
namespace Ichnaea\WebApp\PredictionBundle\Services;

require_once __DIR__.' /../../../../../../amqp/php/vendor/autoload.php';
#require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';

use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaService;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix as PredictionMatrix;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as MatrixUtils;
use Ichnaea\Amqp\Model\BuildModelsRequest as BuildModelsRequest;
use Ichnaea\Amqp\Model\PredictModelsRequest as PredictModelsRequest;
use Ichnaea\Amqp\Model\PredictModelsResponse as PredictModelsResponse; 
use Ichnaea\Amqp\Model\PredictModelsResult as PredictModelsResult;
use Ichnaea\Amqp\Connection as Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;

class PredictionService
{
	protected $em;
	protected $con;
	
	/**
	 * Service constructor
	 * 
	 * @param EntityManager $em
	 * @param String $connection_user
	 * @param String $connection_pass
	 * @param integer $connection_host
	 */
	public function __construct(EntityManager $em, $connection_user, $connection_pass, $connection_host){
		$this->em   = $em;
		$this->con  = new Connection($connection_user.':'.$connection_pass.'@'.$connection_host);
	}
	
	/**
	 * Reads a csv and builds a prediction matrix object and save it 
	 * 
	 * @param integer $training_id
	 * @param string $name
	 * @param string $content
	 * @param integer $owner_id
	 * @param integer $prediction_id: if not null, will update the prediction matrix with the prediction_id 
	 */
	public function createMatrixPredictionFromCSV($training_id, $name, $description, $content, $owner_id, $prediction_id = NULL) 
	{
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		$predictionMatrix;
		
		//new prediction matrix
		if ( is_null($prediction_id) === TRUE)
		{
		  $predictionMatrix = new PredictionMatrix();		
		  $predictionMatrix->setName($name);
		  $predictionMatrix->setDescription($description);
		  $predictionMatrix->setTraining($training);
		  $predictionMatrix->setPredictionsResult(array());
		}
		//already created, first we need to clean it and we must updated
		else 
		{
			$predictionMatrix = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->find($prediction_id);
			
			//Delete all samples
			foreach ($predictionMatrix->getRows() as $sample){
				$predictionMatrix->removeRow($sample);
				$this->em->remove($sample);
			}	
		}
		
		$index=0;
		
		//m_columns: the number of columns of the matrix
		$m_columns = count($training->getMatrix()->getColumns());
		$origin = FALSE;
		
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line){
			$max_colums=0;
			//On headers, init indexs counters
			if($index == 0) {
				$columns    = explode(";", $line);
				
				//IF in the columns[m_columns+1] is written "ORIGIN" instead of a variable name means that matrix has the ORIGIN COLUMN
				if (isset($columns[$m_columns+1])){
					if (strpos($columns[$m_columns+1], "ORIGIN") !== false ){
						$origin = TRUE;
						$m_columns++;
					}
				}
				
			}
			else{
				//If not an empty line
				if($line != ''){
					$columns = explode(";", $line);
		
					//First Column: Definition of sample
					$sample = new PredictionSample();
					$sample->setName($this->cleanStringCSV($columns[0]));
					$sample->setMatrix($predictionMatrix);
					foreach($columns as $key => $string) {
						$columns[$key] = $this->cleanStringCSV($string);
					}
					
					//manage origin column
					if ($origin == TRUE){
						$sample->setSamples(array_slice($columns, 1, $m_columns-1, TRUE));
						if(isset($columns[$m_columns])) $sample->setOrigin($this->cleanStringCSV($columns[$m_columns]));
					}
					else{
						$sample->setSamples(array_slice($columns, 1, null, TRUE));
					}
					
					//set as sample
					$predictionMatrix->addRow($sample);
				}	
			}
			$index++;
		}
		
		#Attach to the user
		$userRepository = $this->em->getRepository('UserBundle:User');
		$user = $userRepository->find($owner_id);
		$predictionMatrix->setOwner($user);
		
		$this->em->persist($predictionMatrix);
		$this->em->flush();
		return $predictionMatrix->getId();
	}
	
	/**
	 * Get the prediction object with id $prediction id 
	 * 
	 * @param int $matrix_id
	 * @param int $training_id
	 * @param int $prediction_id
	 */
	public function getPredictionMatrix($matrix_id, $training_id, $prediction_id)
	{
		return $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->find($prediction_id);
	}
	
	/**
	 * Gets all predictions from a training with id $training_id
	 * 
	 * @param int $training_id
	 */
	public function getPredictionsFromTraining($training_id)
	{
		return $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->findBy(array('training'=>$training_id));
	}
	
	/**
	 * Gets all prediction from a user with id $user_id
	 * 
	 * @param int $user_id
	 */
	public function getPredictionsByUser($user_id)
	{
		return $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->findByOwner($user_id);
	}
	
	/**
	 * Returns the complete list of predictions
	 */
	public function getSystemPredictions($offset = 0){
		$items = 30;
		$repository = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix');
		$predictions = $repository->findBy(array(), array(), $items, $offset * $items);
		return $predictions;
	}
	
	/**
	 * Builds a dataset and sends it to the queue. If it couldn't be sent, it will marked as pending 
	 * 
	 * @param unknown $matrix_id
	 * @param unknown $training_id
	 * @param unknown $prediction_id
	 * @return unknown
	 */
	public function sendPrediction($matrix_id, $training_id, $prediction_id)
	{
		$predict_matrix = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->find($prediction_id);
		
		//Just prepare the data and the cue
		$data = MatrixUtils::buildDatasetFromMatrix(
				NULL,
				'simple',
				$predict_matrix->getTraining()->getMatrix()->getColumns(),
				$predict_matrix->getRows()
		);
		
		//@TODO: read training data zip
		$training_file = "/opt/lampp/htdocs/ichnaea/ichnaea_data/trainings/".$training_id."/r_data.zip";
		$fd            = fopen($training_file, "r");
		$content       = fread($fd, filesize($training_file));
		$data['data']  = $content;
		
		//Still preparing the dataset	
		$data['type']  = 'predict-models';
			
		//build the data array for the dataset
		$model         = PredictModelsRequest::fromArray($data);		
		//... set the new request id for the cue...
		$predict_matrix->setRequestId($model->getId());
		 
		try {
			$this->con->open();
			$this->con->send($model);
			$this->con->close();
			//set status as sent
			$predict_matrix->setStatusAsSent();
		}
		catch (\Exception $e)
		{
			//if any connection problem... set the status as pending
			$predict_matrix->setStatusAsPending();
		}
				
		//Persist the entity
		$this->em->persist($predict_matrix);
		$this->em->flush();
		return $predict_matrix;
	}
	
	/**
	 * Updates a training. Only called by the consumer. 
	 * 
	 * @param int $requestId
	 * @param decimal $progress
	 * @param string $status
	 * @param mixed $data
	 */
	public function updatePrediction($requestId, $progress, $status, $data)
	{
		$prediction = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->findOneBy(array('requestId'=>$requestId));
		if ($prediction instanceof PredictionMatrix)
		{
			$prediction->setProgress($progress);
			$prediction->setError($status);
			echo "*** Service updating data: ***\n";
			echo "*BEGIN INCREMENTAL*";
			var_dump($data);
			echo "*END INCREMENTAL*";
			//Saving all incremental results
			$inc_result = $prediction->getPredictionsResult();
			if ( $data !== 'NULL' ){
				array_push($inc_result,$data);
				echo "*** TOTAL **\n";
				var_dump($inc_result);
			}
			$prediction->setPredictionsResult($inc_result);			
			if ($progress == '1.0')	$prediction->setStatusAsFinished();
			$this->em->persist($prediction);
			$this->em->flush();
		}
		
	}

	/**
	 * Returns the prediction results as array. Depends on the mode:
	 * - mode 'asHTMLTable':  will return an array on HTML's code according to toHTML() function of Ichnaea\Amqp\Model\PredictModelsResult 
	 * - mode 'objects': will return an array on Ichnaea\Amqp\Model\PredictModelsResult
	 * @param int $matrix_id
	 * @param int $training_id
	 * @param int $prediction_id
	 * @param string $mode: 'asHTMLTables', 'objects'
	 * @return multitype - array of objects 
	 */
	public function getPredictionResults($matrix_id, $training_id, $prediction_id, $mode = 'objects')
	{
		$prediction = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->find($prediction_id);
		$result = $prediction->getPredictionsResult();
		$results_objects = array();
		foreach($result as $k=>$v)
		{
			//Improve as callable function
			switch($mode){
				case 'asHTMLTables':
					array_push($results_objects,$v->toHtml());
					break;		
				default:
					array_push($results_objects,$v);
					break;
			}
		}	
		return $results_objects;
	}
	
	/**
	 * We must put it into a Utils beacuse is duplicated code
	 * 
	 * @param string $string
	 * @return mixed
	 */
	private function cleanStringCSV($string){
		$invalid_chars = array('\'','"');
		return str_replace($invalid_chars, "", $string);
	}
}

?>
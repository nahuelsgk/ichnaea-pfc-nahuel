<?php 
namespace Ichnaea\WebApp\PredictionBundle\Services;

require_once __DIR__.' /../../../../../../amqp/php/vendor/autoload.php';
#require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';

use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaService;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix as PredictionMatrix;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as MatrixUtils;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;

class PredictionService
{
	protected $em;
	
	/**
	 * Service constructor
	 * 
	 * @param EntityManager $em
	 * @param String $connection_user
	 * @param String $connection_pass
	 * @param integer $connection_host
	 */
	public function __construct(EntityManager $em){
		$this->em   = $em;
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
		  //die("prediction_id is null");
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
			//Reset the basic info
			$predictionMatrix->setName($name);
			$predictionMatrix->setDescription($description);
			
		
			//If we need to update de content, reset the samples 
			if ($content != '')
			{
				//Delete all samples
				foreach ($predictionMatrix->getRows() as $sample){
					$predictionMatrix->removeRow($sample);
					$this->em->remove($sample);
				}		
			}
		}
		
		//If we need to update the content
		if ($content != '')
		{
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
						$sample->setName(MatrixUtils::cleanStringCSV($columns[0]));
						$sample->setMatrix($predictionMatrix);
						foreach($columns as $key => $string) {
							$columns[$key] = MatrixUtils::cleanStringCSV($string);
						}
						
						//manage origin column
						if ($origin == TRUE){
							$sample->setSamples(array_slice($columns, 1, $m_columns-1, TRUE));
							if(isset($columns[$m_columns])) $sample->setOrigin(MatrixUtils::cleanStringCSV($columns[$m_columns]));
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
	 * Deletes a prediction
	 * 
	 * @param int $matrix_id - useless
	 * @param int $training_id - useless
	 * @param int $prediction_id - prediction id
	 * @param int $user_id - user who performs the action
	 * @return boolean
	 */
	public function deletePrediction($matrix_id, $training_id, $prediction_id, $user_id)
	{
		$prediction = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->find($prediction_id);
		//delete all samples
		foreach ($prediction->getRows() as $sample){
			$prediction->removeRow($sample);
			$this->em->remove($sample);
		}
		//delete matrix
		$this->em->remove($prediction);
		$this->em->flush();
		return true;
	}
	
}

?>
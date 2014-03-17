<?php 
namespace Ichnaea\WebApp\PredictionBundle\Services;

require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';
use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaService;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as MatrixUtils;
use Ichnaea\Amqp\Model\BuildModelsRequest as BuildModelsRequest;
use Ichnaea\Amqp\Model\PredictModelsRequest as PredictModelsRequest;
use Ichnaea\Amqp\Model\PredictModelsResponse as PredictModelsResponse; 
use Ichnaea\Amqp\Connection as Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;

class PredictionService
{
	protected $em;
	protected $con;
	
	public function __construct(EntityManager $em, $connection_user, $connection_pass, $connection_host){
		$this->em   = $em;
		$this->con  = new Connection($connection_user.':'.$connection_pass.'@'.$connection_host);
	}
	
	/**
	 * 
	 * @param integer $training_id
	 * @param string $name
	 * @param string $content
	 * @param integer $owner_id
	 * @param integer $prediction_id: if not null, will update 
	 */
	public function createMatrixPredictionFromCSV($training_id, $name, $content, $owner_id, $prediction_id = NULL) 
	{
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		$predictionMatrix;
		if ( is_null($prediction_id) === TRUE)
		{
		  $predictionMatrix = new PredictionMatrix();		
		  $predictionMatrix->setName($name);
		  $predictionMatrix->setTraining($training);
		}
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
		
		//@TODO: Depends on requirements
		//$m_columns  = count($training->getColumnsSelected());
		$m_columns = count($training->getMatrix()->getColumns());
		$origin = FALSE;
		
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line){
			$max_colums=0;
			//On headers, init indexs counters
			if($index == 0) {
				$columns    = explode(";", $line);
				
				//m_columns is the number of columns of the matrix
				//if index m_columns(that should be a variable name) is written "ORIGIN",
				//means that matrix has the ORIGIN COLUMN
				if (isset($columns[$m_columns+1])){
					if (strpos($columns[$m_columns+1], "ORIGIN") !== false ){
						$origin = TRUE;
						$m_columns++;
					}
				}
			}
			else{
			//Check empty lines
				if($line != ''){
					$columns = explode(";", $line);
		
					//First Column: Definition of sample
					$sample = new PredictionSample();
					$sample->setName($this->cleanStringCSV($columns[0]));
					$sample->setMatrix($predictionMatrix);
					foreach($columns as $key => $string) {
						$columns[$key] = $this->cleanStringCSV($string);
					}
					if ($origin == TRUE){
						$sample->setSamples(array_slice($columns, 1, $m_columns-1, TRUE));
						if(isset($columns[$m_columns])) $sample->setOrigin($this->cleanStringCSV($columns[$m_columns]));
					}
					else{
						$sample->setSamples(array_slice($columns, 1, null, TRUE));
					}
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
	
	public function getPredictionMatrix($matrix_id, $training_id, $prediction_id)
	{
		return $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->find($prediction_id);
	}
	
	public function getPredictionsFromTraining($training_id)
	{
		return $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->findBy(array('training'=>$training_id));
	}
	
	public function getPredictionsByUser($user_id)
	{
		return $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->findByOwner($user_id);
	}
	
	//@TODO only temporaly until meet with Miguel
	public function sendPrediction($matrix_id, $training_id, $prediction_id)
	{
		$matrix = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->find($prediction_id);
		
		//Just prepare the data and the cue
		$data = MatrixUtils::buildDatasetFromMatrix(
				NULL,
				'simple',
				$matrix->getTraining()->getMatrix()->getColumns(),
				$matrix->getRows()
		);
		
		//@TODO: read training data zip
		$training_file = "/opt/lampp/htdocs/ichnaea/ichnaea_data/trainings/".$training_id."/r_data.zip";
		$fd            = fopen($training_file, "r");
		$content       = fread($fd, filesize($training_file));
		$data['data']  = base64_decode($content);
		
		//build the data array for the dataset
		$model         = PredictModelsRequest::fromArray($data);
		//... set the new request id for the cue...
		$matrix->setRequestId($model->getId());
		//... prepare a connection and send the data
		
		$model = new PredictModelsResponse($model->getId());
		$data = $model->toArray();
		
		try {
			$this->con->open();
			$this->con->send($model);
			$this->con->close();
			//set status as sent
			$matrix->setStatusAsSent();
		}
		catch (\Exception $e)
		{
			//if any connection problem... set the status as pending
			$matrix->setStatusAsPending();
		}
		
		//Persist the entity
		$this->em->persist($matrix);
		$this->em->flush();
		return $matrix;
	}
	
	private function cleanStringCSV($string){
		$invalid_chars = array('\'','"');
		return str_replace($invalid_chars, "", $string);
	}
}

?>
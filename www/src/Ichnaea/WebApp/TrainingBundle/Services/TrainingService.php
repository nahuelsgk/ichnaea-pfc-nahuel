<?php 
namespace Ichnaea\WebApp\TrainingBundle\Services;

//@TODO: change autoload forms
require_once __DIR__.' /../../../../../../amqp/php/vendor/autoload.php';
#require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';

use Doctrine\ORM\EntityManager as SymfonyEM;
use Ichnaea\WebApp\TrainingBundle\Entity\Training;
use Ichnaea\WebApp\TrainingBundle\Model\TrainingValidation;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as MatrixUtils;
use Ichnaea\Amqp\Model\BuildModelsRequest as BuildModelsRequest;
use Ichnaea\Amqp\Model\BuildModelsResponse as BuildModelsResponse;
use Ichnaea\Amqp\Connection as Connection;
use Ichnaea\WebApp\PredictionBundle\Services\PredictionService as PredictionService;
use Ichnaea\WebApp\PredictionBundle\Services\TrainingQueueService as TrainingQueueService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
 
/**
 * 
 * @author Nahuel Velazco
 *
 */
class TrainingService{
	
	protected $em;
	protected $mfs;
	protected $con;
	protected $data_path;
	
	/**
	 * Constructor service
	 * 
	 * @param SymfonyEM $em
	 * @param String $connection_user
	 * @param String $connection_pass
	 * @param String $connection_host
	 * @param int $data_path
	 */
	public function __construct(SymfonyEM $em, $connection_user, $connection_pass, $connection_host, $data_path)
	{
		$this->em         = $em;
		if(!is_null($connection_user)){
		  $this->con        = new Connection($connection_user.':'.$connection_pass.'@'.$connection_host); 
		  $this->data_path = $data_path;
		}
	}

	/**
	 * Resend a training to the queue
	 * 
	 * @param int $training_id
	 */
	public function resendTraining($training_id) 
	{
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		
		//Just prepare the data and the cue
		$data = MatrixUtils::buildDatasetFromMatrix(
				$training->getMatrix(),
				'simple',
				$training->getMatrix()->getColumns(),
				$training->getMatrix()->getRows()
		);
		
		//build the data array for the dataset
		$model = BuildModelsRequest::fromArray($data);
		
		//... set the new request id for the cue...
		$training->setRequestId($model->getId());
		
	    //Clean the status, errors, progress 
		$training->setError('');
		$training->setProgress(0);
		
		//... prepare a connection and send the data
		try{
		  $this->con->open();
		  $this->con->send($model);
		  $this->con->close();
		  $training->setStatusAsSent();
		}
		catch (\Exception $e){
		  $training->setStatusAsPending();	
		}
		
		//Persist the entity
		$this->em->persist($training);
		$this->em->flush();
		
		return $training->getId();
	}
	
	/**
	 * Creates a training and send it to the queue
	 * 
	 * @param integer $matrix_id
	 * @param integer $trainer_id
	 * @param string $name
	 * @param string $description
	 * @param Array(\Columns) $columns_selection
	 * @return \Ichnaea\WebApp\TrainingBundle\Model\TrainingValidation
	 */
	public function createTraining($matrix_id, $trainer_id, $name, $description = NULL, $columns_selection = NULL, $origin = NULL)
	{
										
		$trainer = $this->em->getRepository('UserBundle:User')->find($trainer_id);
		$matrix  = $this->em->getRepository('MatrixBundle:Matrix')->find($matrix_id);
		
		//Prepare data for the queue
		$data = MatrixUtils::buildDatasetFromMatrix(
				NULL,
				'simple',
				$matrix->getColumns(),
				$matrix->getRows()
		);
  
        //build the data array for the dataset
        $model = BuildModelsRequest::fromArray($data);
        		
		//Create the training entity
		$training = new Training();										
		$training->setName($name);
		$training->setTrainer($trainer);
		$training->setMatrix($matrix);
		
		if(!empty($description)){
			$training->setDescription($description);
		}
		
	    if(!empty($columns_selection))	{
	    	foreach($columns_selection as $k => $v){
	    		$column = $this->em->getRepository("MatrixBundle:VariableMatrixConfig")->find($v);
	    		$training->addColumnsSelected($column);
	    	}
	    }
	    if($origin != NULL)	$training->setOrigin($origin);
	    
		//... set the request id for the cue...
		$training->setRequestId($model->getId());

		//Validates the training
		$validation = new TrainingValidation($training);
		$validation->validate();
		if ($validation->valid() == FALSE) return $validation;
		
		//... prepare a connection and send the data
		try {
		  $this->con->open();
		  $this->con->send($model);
		  $this->con->close();
		  //set status as sent		
		  $training->setStatusAsSent();
		}
		catch (\Exception $e)
		{
		  //if any connection problem... set the status as pending
		  $training->setStatusAsPending();
		}
		//Persist the entity
		$this->em->persist($training);
		$this->em->flush();
		$validation->setTraining($training);
		return $validation;
	}

	
	/*** GETTERS ***/
	
	/**
	 * Gets one training
	 *  
	 * @param int $training_id
	 */
	public function getTraining($training_id)
	{
		return $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
	}
	
	/**
	 * Get a list of all trainings in the systems
	 */
	public function getTrainingList()
	{
		return $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->findAll();
	}
	
	/**
	 * Delete one training and all its predictions 
	 */
	public function deleteTraining($matrix_id, $training_id, $user_id)
	{
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		
		//delete all predictions
		$predictionService = new PredictionService($this->em);
		$predictions       = $predictionService->getPredictionsFromTraining($training_id);
		foreach($predictions as $prediction)
		{
			$predictionService->deletePrediction($matrix_id, $training_id, $prediction->getId(), $user_id);
		}
		
		$this->em->remove($training);
		$this->em->flush();
		return true;
	}

	
	/**
	 * Updates a training. Only called by command and cli
	 * 
	 * @param int $requestId
	 * @param decimal $progress
	 * @param string(enum) $status
	 * @param binary $data - binary encoded in base64
	 */
	public function updateTraining($requestId, $progress, $status, $data)
	{
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->findOneBy(array('requestId'=>$requestId));
		if ($training instanceof Training)
		{
			$training->setProgress($progress);
			$training->setError($status);
			
			//@TODO: in some situations do no change its state
			if ($progress == '1.0') $training->setStatusAsFinished();
			
			$this->saveDataToFile($training->getId(), base64_decode($data));
			$this->em->flush();
		}
	}
	
	/**
	 * Simple test to check if the queue is available
	 * 
	 * @return boolean
	 */
	public function queueTest(){
		$result = array('status' => true, 'message'=>null);
		try{
		  $this->con->open();
		  $this->con->close();
		}
		catch(\Exception $e)
		{
			$result['status'] = false;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}
	
	
	/**
	 * List of trainings of a user
	 * 
	 * @param int $user_id
	 */
	public function getTrainingsByUser($user_id)
	{
		$user = $this->em->getRepository('UserBundle:User')->find($user_id);
		return $user->getTrainings();
	}
	
	/**
	 * Get trainings of a user which has errors or are pending
	 * 
	 * @param int $user_id
	 */
	public function getPendingOrErrorTrainingsByUser($user_id)
	{
		$qb = $this->em->createQueryBuilder();
		
		$query = $qb->select('t')
		->from('IchnaeaWebAppTrainingBundle:Training','t')
		->where('t.trainer = :user_id')
		->andwhere(
			$qb->expr()->orx(
				't.status = :sent',
				$qb->expr()->andx(
					"t.status = :finished",
					"t.error <> :null"
				),
				't.status = :pending'
			)
		)
		->setParameters(array(
				'user_id'  => $user_id,
				':sent'    => 'sent',
				'finished' => 'finished',
				':null'    => '',
				':pending' => 'pending'
				)
		)
		->getQuery();
		
		return $query->getResult();
	}
	
	/**
	 * Return a list of predictable training list 
	 */
	public function getTrainableTrainingList()
	{
		$query = $this->em
		->createQueryBuilder()
		->select('t, m, u')
		->from('IchnaeaWebAppTrainingBundle:Training','t')
		->join('t.matrix', 'm')
		->join('t.trainer', 'u')
		->where('t.progress = :prog')
		->andwhere('t.error = :error ')
		->setParameters(array('prog' => '1.00', 'error' => ''))
		->getQuery();
		
		return $query->getResult();
		
	}
	
	/**
	 * Function that save the data param in a file
	 * 
	 * @param int $training_id
	 * @param binary $data
	 */
	private function saveDataToFile($training_id, $data)
	{
		$folder = $this->buildTrainingDataPathFolder($training_id);
		$fs = new Filesystem();
		if ($fs->exists($folder) == FALSE){
			$fs->mkdir($folder);
		}
		$abs_path = $this->buildTrainingDataPathRdata($training_id);
		$fp = fopen($abs_path, 'w');
		//$fp = fopen('/tmp/'.$training_id, 'w');
		fwrite($fp, $data);
		fclose($fp);
		echo "Success!: Saved r_data into $abs_path\n";
	}
	
	/**
	 * Builds a filesystem path where the Rdata binary result is stored
	 *
	 * @param int $training_id
	 * @return string
	 */
	public function getRdataContent($training_id)
	{
		$file = $this->buildTrainingDataPathRdata($training_id);
		return file_get_contents($file);
	}
	
	/**
	 * Function that builds the path folder for a training
	 * 
	 * @param int $training_id
	 * @return string
	 */
	private function buildTrainingDataPathFolder($training_id)
	{
		return $this->data_path.'/trainings/'.$training_id.'/';
	}
	
	/**
	 * Function that builds the path of the file for a training
	 * 
	 * @param int $training_id
	 * @return string
	 */
	private function buildTrainingDataPathRdata($training_id)
	{
		return $this->buildTrainingDataPathFolder($training_id).'r_data.zip';
	}
}
?>
<?php 
namespace Ichnaea\WebApp\TrainingBundle\Services;

//@TODO: change autoload forms
#require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';
require_once __DIR__.'/../../../../../../amqp/php/vendor/autoload.php';
use Doctrine\ORM\EntityManager as SymfonyEM;
use Ichnaea\WebApp\TrainingBundle\Entity\Training;
use Ichnaea\WebApp\TrainingBundle\Model\TrainingValidation;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as MatrixUtils;
use Ichnaea\Amqp\Model\BuildModelsRequest as BuildModelsRequest;
use Ichnaea\Amqp\Model\BuildModelsResponse as BuildModelsResponse;
use Ichnaea\Amqp\Connection as Connection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class TrainingService{
	
	protected $em;
	protected $mfs;
	protected $con;
	protected $data_path;
	
	public function __construct(SymfonyEM $em, $connection_user, $connection_pass, $connection_host, $data_path)
	{
		$this->em         = $em;
		$this->con        = new Connection($connection_user.':'.$connection_pass.'@'.$connection_host); 
		$this->data_path = $data_path;
	}
	
	public function resendTraining($training_id) 
	{
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		
		//Just prepare the data and the cue
		$data = MatrixUtils::buildDatasetFromMatrix($training->getMatrix());
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
	
	public function createTraining($matrix_id, $trainer_id, $name, $description = NULL, $k1 = NULL, $k2 = NULL, 
									$best_models = NULL, $min_size_var_set = NULL, $max_size_var_set = NULL, 
									$type_of_search = NULL)
	{
										
		$trainer = $this->em->getRepository('UserBundle:User')->find($trainer_id);
		$matrix  = $this->em->getRepository('MatrixBundle:Matrix')->find($matrix_id);
		
		//Prepare data for the queue
		$data = MatrixUtils::buildDatasetFromMatrix($matrix);
  
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
		
		if(!empty($k1))               $training->setK1($k1);
		if(!empty($k2))               $training->setK2($k2);
		if(!empty($best_models))      $training->setBestModels($best_models);
		if(!empty($min_size_var_set)) $training->setMinSizeVariableSet($min_size_var_set);
		if(!empty($max_size_var_set)) $training->setMaxSizeVariableSet($max_size_var_set);
		if(!empty($type_of_search))   $training->setTypeOfSearch($type_of_search);
	
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

	public function sendTrainingToQueue($training_id){
		
	}
	
	public function getTraining($training_id){
		return $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
	}
	
	public function deleteTraining($training_id){
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		$this->em->remove($training);
		$this->em->flush();
	}

	
	/**
	 * Only called by command and cli 
	 */
	public function updateTraining($requestId, $progress, $status, $data)
	{
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->findOneBy(array('requestId'=>$requestId));
		if ($training instanceof Training)
		{
			$training->setProgress($progress);
			$training->setError($status);
			//Save the data
			var_dump($progress);
			if ($progress == '1.0'){
				$training->setStatusAsFinished();
			}
			$this->saveDataToFile($training->getId(), base64_decode($data));
			$this->em->flush();
		}
	}
	
	/**
	 * @TODO: probably will be moved to separate service
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
	
	/* FUNCTION TO BUILD PATHES */
	private function buildTrainingDataPathFolder($training_id)
	{
		return $this->data_path.'/trainings/'.$training_id.'/';
	}
	
	private function buildTrainingDataPathRdata($training_id)
	{
		return $this->buildTrainingDataPathFolder($training_id).'r_data.zip';
	}
	
	/**
	 * Simple test to check if the queue is available
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
}
?>
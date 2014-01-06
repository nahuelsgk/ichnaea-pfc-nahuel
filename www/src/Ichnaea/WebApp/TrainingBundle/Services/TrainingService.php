<?php 
namespace Ichnaea\WebApp\TrainingBundle\Services;

//@TODO: change autoload forms
require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';

use Doctrine\ORM\EntityManager as SymfonyEM;
use Ichnaea\WebApp\TrainingBundle\Entity\Training;
use Ichnaea\WebApp\TrainingBundle\Model\TrainingValidation;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as MatrixUtils;
use Ichnaea\Amqp\Model\BuildModelsRequest as BuildModelsRequest;
use Ichnaea\Amqp\Model\BuildModelsResponse as BuildModelsResponse;
use Ichnaea\Amqp\Connection as Connection;


class TrainingService{
	
	protected $em;
	protected $mfs;
	protected $con;
	public function __construct(SymfonyEM $em)
	{
		$this->em  = $em;
		$this->con = new Connection('test:test@localhost'); 
	}
	
	private function buildDataSet($matrix)
	{
		
	}
	
	public function createTraining($matrix_id, $trainer_id, $name, $description = NULL, $k1 = NULL, $k2 = NULL, 
									$best_models = NULL, $min_size_var_set = NULL, $max_size_var_set = NULL, 
									$type_of_search = NULL)
	{
										
		$trainer = $this->em->getRepository('UserBundle:User')->find($trainer_id);
		$matrix  = $this->em->getRepository('MatrixBundle:Matrix')->find($matrix_id);
		
		//Prepare stuff for the queue
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
		$this->con->open();
		$this->con->send($model);
		$this->con->close();
				
		//$model = new BuildModelsResponse($model->getId());
		
		//Persist the entity
		$this->em->persist($training);
		$this->em->flush();
		$validation->setTraining($training);
		return $validation;
	}

	public function getTraining($training_id){
		return $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
	}
	
	public function deleteTraining($training_id){
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		$this->em->remove($training);
		$this->em->flush();
	}

	public function checkTraining($training_id){
		error_log("*** Now lets check the training. ***");

		#Get the training and the matrix id
		$training  = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		$matrix    = $training->getMatrix();
		$matrix_id = $matrix->getId();
		
		//check status
		
		//Check if files already prepared
		
		//if not Prepare files
		
		//Cue its up.
		
		//Cue is ready
		
	}
}
?>
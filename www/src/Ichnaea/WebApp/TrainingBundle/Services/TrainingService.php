<?php 
namespace Ichnaea\WebApp\TrainingBundle\Services;

use Doctrine\ORM\EntityManager;
use Ichnaea\WebApp\TrainingBundle\Entity\Training;
use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaMakefileService as MakefileService;

class TrainingService{
	
	protected $em;
	protected $mfs;
	
	public function __construct(EntityManager $em, MakefileService $mfs){
		$this->em = $em;
		$this->mfs = $mfs;
	}
	
	public function createTraining($matrix_id, $trainer_id, $name, $description = NULL, $k1 = NULL, $k2 = NULL, 
									$best_models = NULL, $min_size_var_set = NULL, $max_size_var_set = NULL, 
									$type_of_search = NULL){
										
		$trainer = $this->em->getRepository('UserBundle:User')->find($trainer_id);
		$matrix = $this->em->getRepository('MatrixBundle:Matrix')->find($matrix_id);
		$training = new Training();										
		$training->setName($name);
		$training->setTrainer($trainer);
		$training->setMatrix($matrix);
		
		if(!empty($description)){
			$training->setDescription($description);
		}
		
		if(!empty($k1)){ 
			$training->setK1($k1);
		}
		
		if(!empty($k2)) 
			$training->setK2($k2);
			
		if(!empty($best_models)) 
			$training->setBestModels($best_models);
			
		if(!empty($min_size_var_set)) 
			$training->setMinSizeVariableSet($min_size_var_set);
			
		if(!empty($max_size_var_set)) 
			$training->setMaxSizeVariableSet($max_size_var_set);
			
		if(!empty($type_of_search)) 
			$training->setTypeOfSearch($type_of_search);
			
		$this->em->persist($training);
		$this->em->flush();
		return $training->getId();
	}

	public function getTraining($training_id){
		return $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
	}

	public function checkTraining($training_id){
		error_log("*** Now lets check the training. ***");

		#Get the training and the matrix id
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		$matrix    = $training->getMatrix();
		$matrix_id = $matrix->getId();
		
		//check status
		$mfs = new MakefileService($this->em);
		$mfs->prepareMatrixFiles($matrix_id, $training_id);
		
		
		//Check if files already prepared
		
		//if not Prepare files
		
		//Cue its up.
		
		//Cue is ready
		
		//
	}
}
?>
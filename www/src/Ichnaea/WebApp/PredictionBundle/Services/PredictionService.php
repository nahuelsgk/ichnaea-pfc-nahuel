<?php 
namespace Ichnaea\WebApp\PredictionBundle\Services;

use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaService;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample;
use Doctrine\ORM\EntityManager;

class PredictionService
{
	protected $em;
	
	public function __construct(EntityManager $em){
		$this->em   = $em;
	}
	
	/**
	 * 
	 * @param unknown $training_id
	 * @param unknown $name
	 * @param unknown $content
	 * @param unknown $owner_id
	 * @param integer $prediction_id: if not null, will update 
	 */
	public function createMatrixPredictionFromCSV($training_id, $name, $content, $owner_id, $prediction_id = NULL) 
	{
		$training = $this->em->getRepository('IchnaeaWebAppTrainingBundle:Training')->find($training_id);
		error_log('Creating a prediction '.$prediction_id);
		$predictionMatrix;
		if ( is_null($prediction_id) === TRUE)
		{
		  error_log("New prediction");
		  $predictionMatrix = new PredictionMatrix();		
		  $predictionMatrix->setName($name);
		  $predictionMatrix->setTraining($training);
		}
		else 
		{
			error_log("Updating prediction");
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
		return $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->find($prediction_id);;
	}
	
	public function getPredictionsFromTraining($training_id)
	{
		return $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->findBy(array('training'=>$training_id));
	}
	
	private function cleanStringCSV($string){
		$invalid_chars = array('\'','"');
		return str_replace($invalid_chars, "", $string);
	}
}

?>
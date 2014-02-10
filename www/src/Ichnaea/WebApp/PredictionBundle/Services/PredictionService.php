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
error_log('Updating '.$prediction_id);
		$predictionMatrix;
		if ( is_null($prediction_id) == FALSE)
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
				$sample->remove();
			}	
		}
		
		$index=0;
		$m_columns  = count($training->getColumnsSelected());
		
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line){
			$max_colums=0;
			//On headers, init indexs counters
			if($index == 0) {
				$columns    = explode(";", $line);
			}
			else{
			//Check empty lines
				if($line != ''){
					$columns = explode(";", $line);
		
					//First Column: Definition of sample
					$sample = new PredictionSample();
					$sample->setName($columns[0]);
					$sample->setMatrix($predictionMatrix);
					$sample->setSamples(array_slice($columns, 1, $m_columns, TRUE));
					$predictionMatrix->addRow($sample);
					if(isset($columns[$m_columns])) $sample->setOrigin($columns[$m_columns+1]);
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
}

?>
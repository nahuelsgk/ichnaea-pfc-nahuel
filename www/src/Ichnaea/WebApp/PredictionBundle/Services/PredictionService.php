<?php 
namespace Ichnaea\WebApp\PredictionBundle\Services;

require_once __DIR__.' /../../../../../../amqp/php/vendor/autoload.php';
#require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';

use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaService;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix as PredictionMatrix;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionColumn;
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
			
		
			//If we need to update the content, reset the samples 
			if ($content != '')
			{
				//Delete all samples
				foreach ($predictionMatrix->getRows() as $sample){
					$predictionMatrix->removeRow($sample);
					$this->em->remove($sample);
				}
				//Delete all columns
				foreach ($predictionMatrix->getColumns() as $column)
				{
					$predictionMatrix->removeColumn($column);
					$this->em->remove($column);
				}
				
			}
		}
		
		//If we need to update the content
		if ($content != '')
		{
			$index=0;
			
			//n_columns: the number of columns of the csv
			$n_columns = 0;
			//m_columns: mark the final values position
			$m_columns = 0;
			//by default the ORIGIN COLUMN dont exists
			$origin = FALSE;
			//by default the DATE COLUMN dont exists
			$date = FALSE;
		
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line){
				//On headers, init indexs counters
				if($index == 0) {
					$columns   = explode(";", $line);
					
					$n_columns = count($columns);
					
					/* Begin Resolving format of CSV  */
					//IF in the columns[m_columns] is written "ORIGIN" 
					//instead of a variable name 
					//means that this csv in the last column species a sample
					if (strpos($columns[$n_columns-1], "ORIGIN") !== false ){
						$origin = TRUE;
						$m_columns = $n_columns-2;
					}
					//IF in the columns[m_columns] is written "DATE"
					//instead of a variable name
					//means that this csv in the last column species a date
					elseif (strpos($columns[$n_columns-1], "DATE") !== false ){
						$date = TRUE;
						$m_columns = $n_columns-2;
					}
					//IF in the columns[m_columns] is written "DATE"
					//instead of a variable name
					//means that this csv in the last column species a date
					elseif (strpos($columns[$n_columns-2], "ORIGIN") !== false && strpos($columns[$n_columns-1], "DATE") !== false){
						$origin = TRUE;
						$date = TRUE;
						$m_columns = $n_columns-3;
					}
					else{
						$m_columns = $n_columns-1;
					}
					
					/* END Resolving format of CSV */
					for($i=1; $i<=$m_columns; $i++){
						$variable_name = MatrixUtils::cleanStringCSV($columns[$i]);
						$variableConfiguration = new PredictionColumn();
						$variableConfiguration->setName($variable_name);
						$variableConfiguration->setPrediction($predictionMatrix);
						$variableConfiguration->setIndex($i);
						
						$columns_trained = $training->getColumnsSelected();
						echo "Creo columna $i<br>";
						//Try to match if the column name has the same name
						if(!empty($columns_trained)){
							foreach($columns_trained as $column_trained){
								if(!is_null($column_trained->getVariable())){
									if($column_trained->getVariable()->getName() == $variable_name){
										$variableConfiguration->setColumnConfiguration($column_trained);
										break;
									}
								}
							}
						}
						$predictionMatrix->addColumn($variableConfiguration);
					}
					//die();
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
						if ($origin == TRUE && $date == FALSE){
							$sample->setSamples(array_slice($columns, 1, $n_columns-2, TRUE));
							if(isset($columns[$n_columns-1])) $sample->setOrigin(MatrixUtils::cleanStringCSV($columns[$n_columns-1]));
						}
						//manage origin and date
						elseif($date == TRUE){
							$sample->setSamples(array_slice($columns, 1, $n_columns-3, TRUE));
							if(isset($columns[$n_columns-2])) $sample->setOrigin(MatrixUtils::cleanStringCSV($columns[$n_columns-2]));
							if(isset($columns[$n_columns-1])) {
								//$convert_date = date_create_from_format(, $columns[$n_columns-1]);
								//var_dump($convert_date);
								//die();
								
								$sample->setDate(\DateTime::createFromFormat('d/n/Y', $columns[$n_columns-1]));
							}
						}
						else{
							$sample->setSamples(array_slice($columns, 1, null, TRUE));
							$sample->setOrigin(MatrixUtils::resolveOriginInSampleName($columns[0]));
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
	public function getSystemPredictions($offset = 0)
	{
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
			//Saving all incremental results
			$inc_result = $prediction->getPredictionsResult();
			if ( $data !== 'NULL' ){
				array_push($inc_result,$data);
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
		
		//delete all columns
		foreach($prediction->getColumns() as $column)
		{
			$prediction->removeColumn($column);
			$this->em->remove($column);
		}
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
	
	/**
	 * Get predictions of a user which has errors or are pending
	 * 
	 * @param int $user_id
	 */
	public function getPendingOrErrorPredictionsByUser($user_id)
	{
		$qb = $this->em->createQueryBuilder();
		
		$query = $qb->select('p')
		->from('IchnaeaWebAppPredictionBundle:PredictionMatrix','p')
		->where('p.owner = :user_id')
		->andwhere(
				$qb->expr()->orx(
						'p.status = :sent',
						$qb->expr()->andx(
								"p.status = :finished",
								"p.error <> :null"
						),
						'p.status = :pending'
				)
		)
		->setParameters(
			array(
					'user_id'  => $user_id,
					':sent'    => 'sent',
					'finished' => 'finished',
					':null'    => '',
					':pending' => 'pending',
		 	)
		)
		->getQuery();
		
		return $query->getResult();
		
	}
	
	/**
	 * Update a prediction sample configuration
	 *
	 * @param unknown $matrix_id
	 * @param unknown $sample_id
	 * @param unknown $new_name
	 * @param string $new_date
	 * @param string $new_origin
	 */
	public function updateSample($matrix_id, $sample_id, $new_name, $new_date = NULL, $new_origin = NULL)
	{
		$sampleRepository = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionSample');
		$sample = $sampleRepository->find($sample_id);
	
		$sample->setName($new_name);
		if(!is_null($new_date)) $sample->setDate(new \DateTime($new_date));
		if(!is_null($new_origin)) $sample->setOrigin($new_origin);
	
		$this->em->persist($sample);
		$this->em->flush();
	}
	
	/**
	 * Updates a data value in the prediction samples array 
	 *
	 * @param int $matrix_id
	 * @param int $sample_id
	 * @param int $index - position in the array to update
	 * @param int $new_data
	 * @return boolean
	 */
	public function updateSamplePredictionData($matrix_id, $sample_id, $index, $new_data)
	{
		$sampleRepository = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionSample');
		$sample = $sampleRepository->find($sample_id);
		$samples_data = $sample->getSamples();
	
		//@TODO: throw exception if is out of limits
		$samples_data[$index] = $new_data;
	
		$sample->setSamples($samples_data);
		$this->em->persist($sample);
		$this->em->flush();
		return true;
	}
	
	/**
	 * Updates a column configuration in a prediction
	 * 
	 * @param int $prediction_id
	 * @param int $column_index
	 * @param string $name
	 * @param int $column_configuration_id
	 * @return boolean
	 */
	public function updateColumnPrediction($prediction_id, $column_index, $name, $column_configuration_id)
	{
		$columnRepository                = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionColumn');
		$variableConfigurationRepository = $this->em->getRepository('MatrixBundle:VariableMatrixConfig');
		$predictionRepostitory           = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix');
		
		error_log("Prediction_id:" . $prediction_id);
		error_log("Index_id:" . $prediction_id);
		error_log("Column configuration:" . $column_configuration_id);
		
		$prediction       = $predictionRepostitory->find($prediction_id);
		$columnPrediction = $columnRepository->findOneBy(array('prediction' => $prediction_id, 'index' => $column_index));
	
		
		if(is_null($columnPrediction)){
			$columnPrediction = new PredictionColumn();
			$columnPrediction->setName($name);
			$columnPrediction->setIndex($column_index);
			$columnPrediction->setPrediction($prediction);
		}
		else{
			$columnPrediction->setName($name);
		}
		if ($column_configuration_id == 0)
		{
			$columnPrediction->setColumnConfiguration(NULL);
		}
		else
		{
			$newVariableConfiguration = $variableConfigurationRepository->find($column_configuration_id);
			$columnPrediction->setColumnConfiguration($newVariableConfiguration);
		}
		$this->em->persist($columnPrediction);
		$this->em->flush();
		return true;
		
	}
}

?>
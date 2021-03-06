<?php

namespace Ichnaea\WebApp\MatrixBundle\Service;

use Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig;
use Doctrine\ORM\EntityManager;
use Ichnaea\WebApp\MatrixBundle\Entity\Season;
use Ichnaea\WebApp\MatrixBundle\Entity\Variable as Variable;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSetComponent;
use Ichnaea\WebApp\MatrixBundle\Entity\Matrix;
use Ichnaea\WebApp\MatrixBundle\Entity\Sample;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as Utils;
use Ichnaea\WebApp\TrainingBundle\Services\TrainingService as TrainingService;
/**
 * 
 * @author Nahuel Velazco
 *
 */
class IchnaeaService{
    
	/**
	 *  
	 * @var EntityManager
	 */
	protected $em;
	
	const CSV_FORMAT_NONE        = 0;
	const CSV_FORMAT_DATE        = 1;
	const CSV_FORMAT_ORIGIN      = 2;
	const CSV_FORMAT_ORIGIN_DATE = 3;
	const CSV_FORMAT_DATE_ORIGIN = 4;
	
	/**
	 * 
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em){
		$this->em = $em;
	} 	
	
	/**
	 * Creates a season  
	 * 
	 * @param string $name
	 * @param string $notes
	 * @param Date $start_date
	 * @param Date $end_date
	 * @param string $content
	 * @return \Ichnaea\WebApp\MatrixBundle\Entity\Season
	 */
	public function createSeason($name, $notes, $start_date, $end_date, $content)
	{
		$season = new Season();
		$season->setName($name);
		$season->setNotes($notes);
		$season->setStartDate(new \DateTime($start_date));
		$season->setEndDate(new \DateTime($end_date));
		$season->setContent($content);
		$this->em->persist($season);
		$this->em->flush();
		return $season;
	}
	
	/**
	 * Updates a season data
	 * 
	 * @param int $id
	 * @param string $name
	 * @param string $notes
	 * @param Date $start_date
	 * @param Date $end_date
	 * @param string $content
	 */
	public function updateSeason($id, $name, $notes, $start_date, $end_date, $content)
	{
		$season = $this->em->getRepository('MatrixBundle:Season')->find($id);
		$season->setName($name);
		$season->setNotes($notes);
		$season->setStartDate(new \DateTime($start_date));
		$season->setEndDate(new \DateTime($end_date));
		$season->setContent($content);
		$this->em->flush();
		return $season->getId();
	}
	
	/**
	 * Get all seasons in the system
	 */
	public function getAllSeasons()
	{
		return $this->em->getRepository('MatrixBundle:Season')->findAll();
	}
	
	/**
	 * Get a season with id $id
	 * 
	 * @param unknown $id
	 * @return unknown
	 */
	public function getSeasonById($id)
	{
    	$season = $this->em->getRepository('MatrixBundle:Season')->find($id);
        return $season;
	}
	
	/**
	 * Get a list of seasons that contains $pattern in the name
	 * 
	 * @param unknown $pattern
	 */
	public function getSeasonByPatterName($pattern)
	{
		//@TODO move to the repository
        $repository = $this->em->getRepository('MatrixBundle:Season');
        $qb = $repository->createQueryBuilder("seasons")
        ->add('select', 's')
        ->add('from', 'MatrixBundle:Season s')
		->add('where', "s.name LIKE ?1")
        ->setParameter(1, "%".$pattern."%");
        $query = $qb->getQuery();
        return 	$query->getArrayResult();
	}
	
	/**
	 * Get all variables in the system
	 */
	public function getAllVariables()
	{
		return $this->em->getRepository('MatrixBundle:Variable')->findAll();
	}
	
	/**
	 * Creates a variable in the system
	 * 
	 * @param string $name
	 * @param string $description
	 */
	public function createVariable($name, $description){
		$variable = new Variable();
		$variable->setName($name);
		$variable->setDescription($description);
		$this->em->persist($variable);
		$this->em->flush();
	}
	
	/**
	 * Updates a variables basic data 
	 * 
	 * @param int $id
	 * @param string $name
	 * @param string $description
	 * @return Variable
	 */
	public function updateVariable($id, $name, $description)
	{
		$variable = $this->em->getRepository('MatrixBundle:Variable')->find($id);
		$variable->setName($name);
		$variable->setDescription($description);
		$this->em->flush();
		return $variable;
	}
	
	/**
	 * Return a variables with the id $variable_id
	 * 
	 * @param int $variable_id
	 */
	function getVariableById($variable_id){
		return $this->em->getRepository('MatrixBundle:Variable')->find($variable_id);	
	}
	
	/**
	 * Creates a season set with a set of season components
	 * 
	 * @param intr $variable_id
	 * @param string $name
	 * @param array[int] $seasonIds - array of ids already in the systems
	 * @param array $components - array to create new seasons for the system. The array structure
	 * 	[0][filename] - name of the season
	 *  [0][type] = 'all_year | summer | spring | autumn | winter'
	 *  [0][content] - content of the season
	 * @return number - A season set created with components(new or already in the system)
	 */
	public function createSeasonSet($variable_id, $name, $alreadySeason = NULL, $components)
	{
		//basic info setting
		$seasonSet = new SeasonSet();
		$seasonSet->setName($name);
		
		$variable = $this->em->getRepository('MatrixBundle:Variable')->find($variable_id);
		$seasonSet->setVariable($variable);
		$this->em->persist($seasonSet);
		
		$seasonRepository = $this->em->getRepository('MatrixBundle:Season');
		
		if(!is_null($alreadySeason)){
			//... search in the associative array the id and the type
			$type     = reset($alreadySeason);
			$seasonId = key($alreadySeason);
			$season   = $seasonRepository->find($seasonId);
			//... search if it was already a component
		
			$season = $this->getSeasonById($seasonId);
			$seasonSetComponent = new SeasonSetComponent();
			$seasonSetComponent->setSeason($season);
			$seasonSetComponent->setSeasonSet($seasonSet);
			$seasonSetComponent->setSeasonType($type);
			$this->em->persist($seasonSetComponent);
		}
		
		//@TODO: replicated code
		foreach($components as $component){
			$filename = $component['filename'];
			$content  = $component['content'];
			$type     = $component['type'];
			
			$season = $this->createSeason($filename, NULL, NULL, NULL, $content);
			
			$seasonSetComponent = new SeasonSetComponent();
			$seasonSetComponent->setSeason($season);
			$seasonSetComponent->setSeasonSet($seasonSet);
			$seasonSetComponent->setSeasonType($type);
			$this->em->persist($seasonSetComponent);
		}
		$this->em->flush();
		
		return $seasonSet->getId();
	}
	
	/**
	 * 
	 * @param unknown $seasonSet_id
	 * @param unknown $name
	 * @param array[int]=>'type' $seasonIds
	 * @param string $components
	 */
    public function updateSeasonSet($seasonSet_id, $name, $alreadySeason = NULL, $components = NULL)
    {
		$seasonSet = $this->em->getRepository('MatrixBundle:SeasonSet')->find($seasonSet_id);
		$seasonSet->setName($name);
		$seasonRepository = $this->em->getRepository('MatrixBundle:Season');
		
		if(!is_null($alreadySeason))
		{
			//First, map the season id of the components to its type into an array for...
			$components_already = $seasonSet->getComponents();
			$components_array;
			foreach($components_already as $component)
			{
				$components_array[$component->getSeason()->getId()] = $component->getSeason();
			}
			//... for search in the associative array the type according to id
			$type     = reset($alreadySeason);
			$seasonId = key($alreadySeason);
			$season   = $seasonRepository->find($seasonId);
			//... and search if it was already a component
			if(!isset($components_array[$seasonId])){
				$season = $this->getSeasonById($seasonId);
				$seasonSetComponent = new SeasonSetComponent();
				$seasonSetComponent->setSeason($season);
				$seasonSetComponent->setSeasonSet($seasonSet);
				$seasonSetComponent->setSeasonType($type);
				$this->em->persist($seasonSetComponent);
			}
		}
		//@TODO: replicated code
		foreach($components as $component){
			$filename = $component['filename'];
			$content  = $component['content'];
			$type     = $component['type'];
				
			$season = $this->createSeason($filename, NULL, NULL, NULL, $content);
				
			$seasonSetComponent = new SeasonSetComponent();
			$seasonSetComponent->setSeason($season);
			$seasonSetComponent->setSeasonSet($seasonSet);
			$seasonSetComponent->setSeasonType($type);
			
			$this->em->persist($seasonSetComponent);
		}
		$this->em->flush();
		return $seasonSet->getId();
	}

	/**
	 * Returns an array of season set of a variable
	 * @param id $variable_id
	 */
	public function getVariableSeasonSets($variable_id)
	{
		$variable = $this->em->getRepository('MatrixBundle:Variable')->find($variable_id);
		return $variable->getSeasonSet();
	}

	/**
	 * 
	 * @param int $id
	 * @return seasonSet
	 */
	public function getSeasonSet($id){
		$season_set = $this->em->getRepository('MatrixBundle:SeasonSet')->find($id);
		return $season_set;
	}
	
	/**
	 * Delete a SeasonSet and its files
	 * 
	 * @param int $season_set_id
	 * @return boolean: FALSE if it is in used in any matrix, TRUE if it could removed
	 */
	public function deleteSeasonSetCascade($season_set_id)
	{
		$seasonSet = $this->em->getRepository('MatrixBundle:SeasonSet')->find($season_set_id);
		
		//Check if is used in a matrix
		$used = $this->seasonSetIsUsedInAnyMatrix($seasonSet);
		if ($used == true) return false;
		//@delete this next two lines if lines above works!
		//$columns_in_matrix_using_this_season_set = $this->em->getRepository('MatrixBundle:VariableMatrixConfig')->findBy(array('seasonSet' => $seasonSet));
		//if (count($columns_in_matrix_using_this_season_set) > 0) return false;
		
		$components = $seasonSet->getComponents();
		//Check if a components has a shared season set....
		foreach ($components as $component){
			$season = $component->getSeason();
			$components_by_season = $this->em->getRepository('MatrixBundle:SeasonSetComponent')->findBy(array('season' => $season));
			//if has it just removed as a a component
			if (count($components_by_season) > 1){
				$this->em->remove($component);
			}
			//otherwise remove it
			elseif($components_by_season = 1){
				$this->em->remove($component);
			   	$this->em->remove($component->getSeason());
			}
		}
		$this->em->remove($seasonSet);
		$this->em->flush();
		return true;
	}
	
	/**
	 * 
	 * @param unknown $variable_id
	 * @param unknown $season_set_id
	 * @param unknown $component_id
	 */
	public function deleteSeasonSetComponent($variable_id, $season_set_id, $component_id)
	{
		$seasonSetComponent = $this->em->getRepository('MatrixBundle:SeasonSetComponent')->find($component_id);
		$this->em->remove($seasonSetComponent);
		$this->em->flush();
	}
	
	/**
	 * Checks if a season set is used in any matrix
	 *  
	 * @param unknown $season_set_id
	 * @return boolean
	 */
	private function seasonSetIsUsedInAnyMatrix($seasonSet)
	{
		$columns_in_matrix_using_this_season_set = $this->em->getRepository('MatrixBundle:VariableMatrixConfig')->findBy(array('seasonSet' => $seasonSet));
		if (count($columns_in_matrix_using_this_season_set) > 0) return true;
		return false;
	}
	
	/**
	 * Checks if a season set has been trained
	 * @param unknown $sesonSet
	 */
	private function seasonSetHasBeenTrainedInAnyMatrix($seasonSet)
	{
		$columns_that_uses_this_season_set = $this->em->getRepository('MatrixBundle:VariableMatrixConfig')->findBy(array('seasonSet' => $seasonSet));
		foreach($columns_that_uses_this_season_set as $column){
			if (count($column()->getMatrix->getTraining()) > 0) return true;
		}
		return false;
	}
	
	/**
	 * 
	 * @param int $variable_id
	 * @param int $season_set_id
	 * @param int $component_id
	 */
	public function deleteCompleteSeasonSetComponent($variable_id, $season_set_id, $component_id)
	{
		$seasonSetComponent = $this->em->getRepository('MatrixBundle:SeasonSetComponent')->find($component_id);
		
		//Check if this season it is in used in a matrix
		$seasonSet = $this->em->getRepository('MatrixBundle:SeasonSet')->find($season_set_id);
		
		//Check if is used in a matrix
		$used = $this->seasonSetHasBeenTrainedInAnyMatrix($seasonSet);
		if ($used == true) return false;
		
		//Check if it is shared by any other season set
		$season = $seasonSetComponent->getSeason();
		$components_by_season = $this->em->getRepository('MatrixBundle:SeasonSetComponent')->findBy(array('season' => $season));
		//if has it, only can be removed as a a component
		if (count($components_by_season) > 1){
			return false;
		}
		
		$this->em->remove($seasonSetComponent);
		$this->em->remove($seasonSetComponent->getSeason());
		$this->em->flush();
		return true;
	}
	
	/**
	 * 
	 * @param unknown $name
	 * @param unknown $content
	 * @param unknown $owner_id
	 * @return number
	 */
	public function createMatrixFromCSVContent($name, $content, $owner_id)
	{
		$matrix = new Matrix();
		$matrix->setName($name);

		$this->buildMatrixFromCSV($matrix, $content);
		
		#Attach to the user
		$userRepository = $this->em->getRepository('UserBundle:User');
		$user = $userRepository->find($owner_id);
		$matrix->setOwner($user);
		
		$this->em->persist($matrix);
		$this->em->flush();
	
		return $matrix->getId();
	}

	/**
	 * Builds a matrix entity from the content. Content is a csv with concrete pattern. Must be ; separated
	 * NO_MATTER_WHAT ; COLUMN_ALIAS ; COLUMN ALIAS ; .....; "ORIGIN"(OPTIONAL BUT SHOULD BE MANDATORY)
	 * SAMPLE_NAME    ; VALUES       ; VALUES       ; .....; "STRING"(MANDATORY IF "ORIGIN" IS PRESENT )
	 * 
	 * @TODO/ Must validate the csv format also...
	 * @TODO/ Must be move into Utils 
	 * @param Refence Matrix $matrix
	 * @param String $content
	 */
	private function buildMatrixFromCSV(&$matrix, $content){
		$index=0;
		$origin = FALSE;
		
		//n_columns: the number of columns of the csv
		$n_columns = 0;
		//m_columns: mark the final values position
		$m_columns = 0;
		//by default the ORIGIN COLUMN dont exists
		$origin = FALSE;
		//by default the DATE COLUMN dont exists
		$date = FALSE;

		$csv_format = self::CSV_FORMAT_NONE;
		
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line){
		
			//First row: variables alias
			if($index == 0){
				$columns    = explode(";", $line);
				
				$n_columns  = count($columns);
					
				/* Begin Resolving format of CSV  */
				//IF in the columns[m_columns] is written "ORIGIN" 
				//instead of a variable name 
				//means that this csv in the last column species a sample
				if (strpos($columns[$n_columns-1], "ORIGIN") !== false && strpos($columns[$n_columns-2], "DATE") === false)
				{
					$csv_format = self::CSV_FORMAT_ORIGIN;
					$m_columns = $n_columns-2;
				}
				
				//IF in the columns[m_columns] is written "DATE"
				//instead of a variable name
				//means that this csv in the last column species a date
				elseif (strpos($columns[$n_columns-1], "DATE") !== false && strpos($columns[$n_columns-2], "ORIGIN") === false)
				{
					$csv_format = self::CSV_FORMAT_DATE;
					$m_columns = $n_columns-2;
				}
				
				//IF in the columns[m_columns] is written "DATE"
				//instead of a variable name
				//means that this csv in the last column species a date
				elseif (strpos($columns[$n_columns-2], "ORIGIN") !== false && strpos($columns[$n_columns-1], "DATE") !== false)
				{
					$csv_format = self::CSV_FORMAT_ORIGIN_DATE;
					$m_columns = $n_columns-3;
				}
				
				elseif (strpos($columns[$n_columns-2], "DATE") !== false && strpos($columns[$n_columns-1], "ORIGIN") !== false)
				{
					$csv_format = self::CSV_FORMAT_DATE_ORIGIN;
					$m_columns = $n_columns-3;
				}
				else{
					$csv_format = self::CSV_FORMAT_NONE;
					$m_columns = $n_columns-1;
				}
				
				//Exclude first column
				for($i=1; $i<=$m_columns; $i++)
				{
					$variable_name = MatrixUtils::cleanStringCSV($columns[$i]);
					$variableConfiguration = new VariableMatrixConfig();
					$variableConfiguration->setName($variable_name);
					$variableConfiguration->setMatrix($matrix);
					
					//if the name of the column is the same as a variable in the system => get the variable, add it and assign the first season set of this variable
					$variable = $this->em->getRepository('MatrixBundle:Variable')->findOneBy(array('name' => $variable_name));
					if ($variable instanceof Variable)
					{
						$variableConfiguration->setVariable($variable);
						if ($variable->getSeasonSet()->first() instanceof SeasonSet) $variableConfiguration->setSeasonSet($variable->getSeasonSet()->first());	
					}
					
					$matrix->addColumn($variableConfiguration);
					$max_columns = $i-1;
				}
			}
		
			//lLater are all the samples
			else{
				//Check empty lines
				if($line != ''){
					$columns    = explode(";", $line);
		
					//First Column: Definition of sample
					$sample = new Sample();
					$sample->setName(Utils::cleanStringCSV($columns[0]));
					$sample->setMatrix($matrix);
					foreach($columns as $key => $string) 
					{
						$columns[$key] = Utils::cleanStringCSV($string);
					}
				
					if ($csv_format == self::CSV_FORMAT_ORIGIN)
					{
						$sample->setSamples(array_slice($columns, 1, $n_columns-2, TRUE));
						if (isset($columns[$n_columns-1]) ) 
							$sample->setOrigin(Utils::cleanStringCSV($columns[$n_columns-1]));
						else 
							$sample->setOrigin(Utils::resolveOriginInSampleName($columns[0]));
					}
					
					//manage origin and date
					elseif($csv_format == self::CSV_FORMAT_ORIGIN_DATE)
					{
						$sample->setSamples(array_slice($columns, 1, $n_columns-3, TRUE));
						if(isset($columns[$n_columns-2])) 
							$sample->setOrigin(MatrixUtils::cleanStringCSV($columns[$n_columns-2]));
						else
							$sample->setOrigin(Utils::resolveOriginInSampleName($columns[0]));
						
						if($columns[$n_columns-1] != '')
							$sample->setDate(\DateTime::createFromFormat('d/n/Y', $columns[$n_columns-1]));
						
					}
					
					//manage origin and date
					elseif($csv_format == self::CSV_FORMAT_DATE_ORIGIN)
					{
						$sample->setSamples(array_slice($columns, 1, $n_columns-3, TRUE));
						if($columns[$n_columns-2] != '')
							$sample->setDate(\DateTime::createFromFormat('d/n/Y', $columns[$n_columns-2]));
						
						if(isset($columns[$n_columns-1]))
							$sample->setOrigin(MatrixUtils::cleanStringCSV($columns[$n_columns-1]));
						else
							$sample->setOrigin(Utils::resolveOriginInSampleName($columns[0]));
					}
					
					//We want all the values until the end
					else
					{
						$sample->setSamples(array_slice($columns, 1, null, TRUE));
						$sample->setOrigin(Utils::resolveOriginInSampleName($columns[0]));
					}
					$matrix->addRow($sample);
				}
			}
			$index++;
		}
	}
	
	/**
	 * Updates the matrix content
	 * 
	 * @param int $matrix_id - id of the matrix
	 * @param string $name - matrix's name
	 * @param string $content - matrix in csv format
	 * @return unknown
	 */
	public function updateMatrixFromCSVContent($matrix_id, $name, $content)
	{
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrix = $matrixRepository->find($matrix_id);
		foreach ($matrix->getRows() as $sample){
			$matrix->removeRow($sample);
		}
		$matrix->setName($name);
		$this->buildMatrixFromCSV($matrix, $content);
		$this->em->persist($matrix);
		$this->em->flush();
		return $matrix_id;
	}
	
	/**
	 * Get all matrixs in the database
	 */
	public function getAllMatrixs(){
		return $this->em->getRepository('MatrixBundle:Matrix')->findAll();
	}
	
	/**
	 * Get a Matrix Entity with an id
	 * 
	 * @param int $id - matrix id
	 */
	public function getMatrix($id){
		return $this->em->getRepository('MatrixBundle:Matrix')->find($id);
	}
	
	/**
	 * Returns the number of samples(rows) of a matrix
	 * 
	 * @param int $id - matrix id
	 * @return number
	 */
	public function getM($id)
	{
		$matrix = $this->em->getRepository('MatrixBundle:Matrix')->find($id);
		$sample = $matrix->getRows();
		
		#We accept that the matrixs are complete,no empty values. So we can get just the first sample 
		$sample_data = $sample[0]->getSamples();
		return count($sample_data); 
	}
	
	/**
	 * Updates matrixs basic configuration
	 * 
	 * @param int $user_id - an user_id that performs the 
	 * @param int $matrix_id - the matrix id
	 * @param string $visibility - visibility
	 * @return unknown
	 */
	public function updateMatrixConfiguration($user_id, $matrix_id, $visibility = TRUE){
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrix = $matrixRepository->find($matrix_id);
		$matrix->setVisible($visibility);
		$this->em->persist($matrix);
		$this->em->flush();
		return $matrix;
	}

	/**
	 * Update matrix's column configuration
	 * 
	 * @param int $matrix_id
	 * @param int $column_id
	 * @param string $new_name - 
	 * @param string $new_variable - id of the variable that is updated
	 * @param int $new_seasonSet - id of the new season set used
	 */
	public function updateMatrixVariable($matrix_id, $column_id, $new_name, $new_variable, $new_seasonSet = NULL){
		$variableConfigurationRepository = $this->em->getRepository('MatrixBundle:VariableMatrixConfig');
		$variableRepository 			 = $this->em->getRepository('MatrixBundle:Variable');
		$seasonSetRepository			 = $this->em->getRepository('MatrixBundle:SeasonSet');
		 
		$column  = $variableConfigurationRepository->find($column_id);
		$column->setName($new_name);
		
		//Update the variables if they are different. By now always update
		$variable = $variableRepository->find($new_variable);
		$column->setVariable($variable);
		
		//If it was updated unset the seasonSet
		if($new_seasonSet){
		 $seasonSet = $seasonSetRepository->find($new_seasonSet);
		 $column->setSeasonSet($seasonSet);
		}
		else{
			$column->setSeasonSet();
		}
		
		$this->em->persist($column);
		$this->em->flush();
	}
	
	/**
	 * Returns if the matrix is updatable
	 * @param int $matrix_id
	 */
	public function matrixIsUpdateable($matrix_id)
	{
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrix = $matrixRepository->find($matrix_id);
		return $matrix->isUpdatable();
	}
	
	/**
	 * Return if the matrix is complete configurated
	 * 
	 * @param int $matrix_id
	 */
	public function matrixIsComplete($matrix_id)
	{
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrix = $matrixRepository->find($matrix_id);
		return $matrix->isComplete();
		
	}
	
	/**
	 * Check if the matrix had trainings
	 * @param int $matrix_id
	 */
	public function matrixIsTrained($matrix_id)
	{
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrix = $matrixRepository->find($matrix_id);
		return $matrix->isTrained();
	
	}
	
	/**
	 * Update a sample configuration
	 * 
	 * @param unknown $matrix_id
	 * @param unknown $sample_id
	 * @param unknown $new_name
	 * @param string $new_date
	 * @param string $new_origin
	 */
	public function updateSample($matrix_id, $sample_id, $new_name, $new_date = NULL, $new_origin = NULL)
	{
		$sampleRepository = $this->em->getRepository('MatrixBundle:Sample');
		$sample = $sampleRepository->find($sample_id);
		
		$sample->setName($new_name);
		if(!is_null($new_date)) $sample->setDate(new \DateTime($new_date));
		if(!is_null($new_origin)) $sample->setOrigin($new_origin);
		
		$this->em->persist($sample);
		$this->em->flush();		
	}
	
	/**
	 * Updates a data value in the samples array
	 * 
	 * @param int $matrix_id
	 * @param int $sample_id
	 * @param int $index - position in the array to update
	 * @param int $new_data
	 * @return boolean
	 */
	public function updateSampleData($matrix_id, $sample_id, $index, $new_data)
	{
		$sampleRepository = $this->em->getRepository('MatrixBundle:Sample');
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
	 * 
	 * @param string $format
	 * @param string $type
	 * @param id $matrix_id
	 * 
	 * Get the matrix as file content.
	 * Param type:
	 * - simple: csv is alias columns, alias samples, and data
	 * - complete: completed configured(not contempleted)
	 * 
	 */
	public function getMatrixAs($format = 'csv', $type='simple', $matrix_id)
	{
		$matrix = $this->getMatrix($matrix_id);
		$dataSet = Utils::buildDatasetFromMatrix(
				$matrix, 
				$type,
				$matrix->getColumns(),
				$matrix->getRows()
		);
		return $dataSet['dataset'];
	}
	
	/**
	 * 
	 * @param integer $matrix_id
	 * @param integer $owner
	 * @param string $new_name
	 * @return Matrix
	 * 
	 * Clones the matrix 
	 * 
	 */
	public function cloneMatrix($matrix_id, $owner, $new_name){
		$matrix_origin = $this->em->getRepository('MatrixBundle:Matrix')->find($matrix_id);
		$clone = clone $matrix_origin;
		$clone->setId(null);
		$clone->setName($new_name);
		$clone->setOwner($owner);
		$clone->setVisible(false);
		foreach ($matrix_origin->getColumns() as $column){
			$variableConfiguration = new VariableMatrixConfig();
		  	$variableConfiguration->setName($column->getName());
		  	$variableConfiguration->setMatrix($clone);
		  	$variableConfiguration->setSeasonSet($column->getSeasonSet());
		  	$variableConfiguration->setVariable($column->getVariable());
		  	$clone->addColumn($variableConfiguration);
		}
		foreach ($matrix_origin->getRows() as $row){
			$sample = new Sample();
			$sample->setName($row->getName());
			$sample->setMatrix($clone);
			$sample->setDate($row->getDate());
			$sample->setOrigin($row->getOrigin());
			$sample->setSamples(array_slice($row->getSamples(), 1, null, TRUE));
			$clone->addRow($sample);
		}
		$this->em->persist($clone);
		$this->em->flush();
		return $clone;
	}
	
	/**
	 * Get matrixs that can be trainables
	 */
	public function getTrainableMatrixs()
	{
			$query = $this->em
			->createQueryBuilder()
			->select('m')
			->from('MatrixBundle:Matrix', 'm')
			->where('m.visible = :vis')	
			->setParameter('vis', '1')
			->getQuery();
			
			return $query->getResult();
	}
	
	/**
	 * Delete a matrix, trainings, services
	 * 
	 * @param int $user_id: user that performs the action
	 * @param int $matrix_id: matrix to delete
	 * @return boolean
	 */
	public function deleteMatrix($user_id, $matrix_id)
	{
		$matrix = $this->getMatrix($matrix_id);
		$trainings = $matrix->getTraining();
		
		$trainingService = new TrainingService($this->em, NULL, NULL, NULL, NULL);
		foreach($trainings as $training){
		  $trainingService->deleteTraining($matrix_id, $training->getId(), $user_id);
		}
		
		//Delete all samples
		$samples = $matrix->getRows();
		foreach($samples as $sample)
		{
			$matrix->removeRow($sample);
			$this->em->remove($sample);
		}
		
		//Delete all variable configuration
		$columns = $matrix->getColumns();
		foreach($columns as $column)
		{
			$matrix->removeColumn($column);
			$this->em->remove($column);
		}
		
		//Delete matrix
		$this->em->remove($matrix);
		$this->em->flush();
		
		return true;
	}
	
}

?>
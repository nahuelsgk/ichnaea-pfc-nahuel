<?php

namespace Ichnaea\WebApp\MatrixBundle\Service;

use Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig;
use Doctrine\ORM\EntityManager;
use Ichnaea\WebApp\MatrixBundle\Entity\Season;
use Ichnaea\WebApp\MatrixBundle\Entity\Variable;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSetComponent;
use Ichnaea\WebApp\MatrixBundle\Entity\Matrix;
use Ichnaea\WebApp\MatrixBundle\Entity\Sample;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as Utils;

class IchnaeaService{
    
	protected $em;
	
	public function __construct(EntityManager $em){
		$this->em = $em;
	} 	
	
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
	
	public function getAllSeasons()
	{
		return $this->em->getRepository('MatrixBundle:Season')->findAll();
	}
	
	public function getSeasonById($id)
	{
    	$season = $this->em->getRepository('MatrixBundle:Season')->find($id);
        return $season;
	}
	
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
	
	public function getAllVariables(){
		return $this->em->getRepository('MatrixBundle:Variable')->findAll();
	}
	
	public function createVariable($name, $description){
		$variable = new Variable();
		$variable->setName($name);
		$variable->setDescription($description);
		$this->em->persist($variable);
		$this->em->flush();
	}
	
	public function updateVariable($id, $name, $description){
		$variable = $this->em->getRepository('MatrixBundle:Variable')->find($id);
		$variable->setName($name);
		$variable->setDescription($description);
		$this->em->flush();
		return $variable;
	}
	
	function getVariableById($variable_id){
		return $this->em->getRepository('MatrixBundle:Variable')->find($variable_id);	
	}
	
	/*
	 * seasonIds: array of ids already in the systems
	 * components: array to create new seasons for the system. The array structure
	 * 	[0][filename]
	 *  [0][type] = 'all_year | summer | spring | autumn | winter'
	 *  [0][content]
	 * 
	 * Result: A season set created with components(new or already in the system)
	 */
	public function createSeasonSet($variable_id, $name, $seasonIds = NULL, $components){
		//basic info setting
		$seasonSet = new SeasonSet();
		$seasonSet->setName($name);
		
		$variable = $this->em->getRepository('MatrixBundle:Variable')->find($variable_id);
		$seasonSet->setVariable($variable);
		$this->em->persist($seasonSet);
		
		$seasonRepository = $this->em->getRepository('MatrixBundle:Season');
		
		//@TODO: dont work, need to be converted into components 
		foreach($seasonIds as $seasonId){
			$season = $seasonRepository->find($seasonId);
			if(!$seasonSet->getSeason()->contains($season)) $seasonSet->addSeason($season);
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
	
    public function updateSeasonSet($seasonSet_id, $name, $seasonIds = NULL, $components = NULL){
		$seasonSet = $this->em->getRepository('MatrixBundle:SeasonSet')->find($seasonSet_id);
		$seasonSet->setName($name);
		$seasonRepository = $this->em->getRepository('MatrixBundle:Season');
		
		//@TODO: dont work, need to be converted into components
		foreach($seasonIds as $seasonId){
			$season = $seasonRepository->find($seasonId);
			if(!$seasonSet->getSeason()->contains($season)) $seasonSet->addSeason($season);
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

	public function getVariableSeasonSets($variable_id){
		$variable = $this->em->getRepository('MatrixBundle:Variable')->find($variable_id);
		return $variable->getSeasonSet();
	}

	public function getSeasonSet($id){
		$season_set = $this->em->getRepository('MatrixBundle:SeasonSet')->find($id);
		return $season_set;
	}
	
	public function getSeasonSetComponents($id)
	{
		$repository = $this->em->getRepository('MatrixBundle:SeasonSetComponent');
		$season_components = $repository->findBySeasonSetId($id);
		$index = 0;
		$components = NULL;
		foreach($season_components as $c){
			$season = $this->em->getRepository('MatrixBundle:Season')->find($c->getSeasonId());
			$components[$index]['name'] = $season->getName();
			$components[$index]['id']   = $season->getId();
			$components[$index]['component_id'] = $c->getId();
			$index++;
		}
		return $components;
	}
	
	public function deleteSeasonSet($seasonSet_id){
		$season = $this->em->getRepository('MatrixBundle:SeasonSet')->find($seasonSet_id);
		$this->em->remove($season);
		$this->em->flush();
	}
	
	public function deleteSeasonSetCascade($season_set_id){
		$seasonSet = $this->em->getRepository('MatrixBundle:SeasonSet')->find($season_set_id);
		
		$components = $seasonSet->getComponents();
		foreach ($components as $component){
			$this->em->remove($component->getSeason());
			$this->em->remove($component);
		}
		$this->em->remove($seasonSet);
		$this->em->flush();
	}
	
	public function deleteSeasonSetComponent($variable_id, $season_set_id, $component_id)
	{
		$seasonSetComponent = $this->em->getRepository('MatrixBundle:SeasonSetComponent')->find($component_id);
		$this->em->remove($seasonSetComponent);
		$this->em->flush();
	}
	
	public function deleteCompleteSeasonSetComponent($variable_id, $season_set_id, $component_id)
	{
		$seasonSetComponent = $this->em->getRepository('MatrixBundle:SeasonSetComponent')->find($component_id);
		$this->em->remove($seasonSetComponent);
		$this->em->remove($seasonSetComponent->getSeason());
		$this->em->flush();
	}
	
	/*
	  
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
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line){
			$max_colums=0;
		
			//First row: variables alias
			if($index == 0){
				$columns    = explode(";", $line);
				$m_columns  = count($columns);
					
				//resolve if the last column is ORIGIN
				if (strpos($columns[$m_columns-1], "ORIGIN") !== false ){
					$origin = TRUE;
					//Just to avoid the last column
					$m_columns --;
				}
					
				//Exclude first column
				for($i=1; $i<$m_columns; $i++){
					//print("Variable alias: ".$columns[$i]."<br>");
					$variableConfiguration = new VariableMatrixConfig();
					$variableConfiguration->setName($columns[$i]);
					$variableConfiguration->setMatrix($matrix);
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
					$sample->setName($columns[0]);
					$sample->setMatrix($matrix);
		
					foreach($columns as $key => $string) {
						$columns[$key] = $this->cleanStringCSV($string);
					}
		
					if ($origin == TRUE){
						$sample->setSamples(array_slice($columns, 1, $m_columns-1, TRUE));
						if(isset($columns[$m_columns]) && $origin)
						{
							$sample->setOrigin($this->cleanStringCSV($columns[$m_columns]));
						}
					}
		
					//We want all the values until the end
					else{
						$sample->setSamples(array_slice($columns, 1, null, TRUE));
					}
					$matrix->addRow($sample);
		
				}
			}
		
			$index++;
		}
	}
	
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
	
	private function cleanStringCSV($string){
		$invalid_chars = array('\'','"');
		return str_replace($invalid_chars, "", $string);	
	}
	
	public function getAllMatrixs(){
		return $this->em->getRepository('MatrixBundle:Matrix')->findAll();
	}
	
	public function getMatrix($id){
		return $this->em->getRepository('MatrixBundle:Matrix')->find($id);
	}
	
	public function getM($id)
	{
		$matrix = $this->em->getRepository('MatrixBundle:Matrix')->find($id);
		$sample = $matrix->getRows();
		
		#We accept that the matrixs are complete,no empty values. So we can get just the first sample 
		$sample_data = $sample[0]->getSamples();
		return count($sample_data); 
	}
	
	public function updateMatrixConfiguration($user_id, $matrix_id, $visibility = TRUE){
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrix = $matrixRepository->find($matrix_id);
		$matrix->setVisible($visibility);
		$this->em->persist($matrix);
		$this->em->flush();
		return $matrix;
	}

	public function updateMatrixVariable($matrix_id, $column_id, $new_name, $new_variable, $new_seasonSet = NULL){
		$variableConfigurationRepository = $this->em->getRepository('MatrixBundle:VariableMatrixConfig');
		$variableRepository 			 = $this->em->getRepository('MatrixBundle:Variable');
		$seasonSetRepository			 = $this->em->getRepository('MatrixBundle:SeasonSet');
		 
		//@TODO Dont know how to get the matrix and update 
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
		
		$this->em->persist($column);
		$this->em->flush();
	}
	
	
	public function matrixIsUpdateable($matrix_id)
	{
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrix = $matrixRepository->find($matrix_id);
		return $matrix->isUpdatable();
	}
	
	public function matrixIsComplete($matrix_id)
	{
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrix = $matrixRepository->find($matrix_id);
		return $matrix->isComplete();
		
	}
	
	public function matrixIsTrained($matrix_id)
	{
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrix = $matrixRepository->find($matrix_id);
		return $matrix->isTrained();
	
	}
	
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
	
	public function getTrainableMatrixs()
	{
			
	}
}

?>
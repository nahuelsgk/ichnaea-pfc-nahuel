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
		return $season->getId();
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
	
	public function createSeasonSet($variable_id, $name, $seasonIds = NULL){
		
		$seasonSet = new SeasonSet();
		$seasonSet->setName($name);
		
		$variable = $this->em->getRepository('MatrixBundle:Variable')->find($variable_id);
		$seasonSet->setVariable($variable);

		$seasonRepository = $this->em->getRepository('MatrixBundle:Season');
		foreach($seasonIds as $seasonId){
			$season = $seasonRepository->find($seasonId);
			if(!$seasonSet->getSeason()->contains($season)) $seasonSet->addSeason($season);
		}
		$this->em->persist($seasonSet);
		$this->em->flush();
		
		return $seasonSet->getId();
	}
	
    public function updateSeasonSet($seasonSet_id, $name, $seasonIds = NULL){
		$seasonSet = $this->em->getRepository('MatrixBundle:SeasonSet')->find($seasonSet_id);
		$seasonSet->setName($name);
		$seasonRepository = $this->em->getRepository('MatrixBundle:Season');
		
		foreach($seasonIds as $seasonId){
			$season = $seasonRepository->find($seasonId);
			if(!$seasonSet->getSeason()->contains($season)) $seasonSet->addSeason($season);
		}
		$this->em->flush();
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
	
	public function deleteSeasonSetComponent($variable_id, $season_set_id, $season_id)
	{
		$seasonSet = $this->em->getRepository('MatrixBundle:SeasonSet')->find($season_set_id);
		$season    = $this->em->getRepository('MatrixBundle:Season')->find($season_id);
		$seasonSet->removeSeason($season);
		$this->em->persist($seasonSet);
		$this->em->flush();
	}
	
	/*
	 * The content of the file: must be ; separated
	 * NO_MATTER_WHAT ; COLUMN_ALIAS ; COLUMN ALIAS ; .....
	 * SAMPLE_NAME    ; VALUES       ; VALUES       ;
	 * 
	 * @TODO/ Must validate the csv format also...
	 */
	public function createMatrixFromCSVContent($name, $content, $owner_id){
		
		$matrix = new Matrix();
		$matrix->setName($name);

		$index=0;
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line){
			$max_colums=0;
			
			//First row: variables alias
			if($index == 0){
				$columns    = explode(";", $line);
			  	$m_columns  = count($columns);
			  	
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
			//Samples
			else{
				//Check empty lines
				if($line != ''){
					$columns    = explode(";", $line);
				
					//First Column: Definition of sample	
					$sample = new Sample();
					$sample->setName($columns[0]);
					$sample->setMatrix($matrix);
					$sample->setSamples(array_slice($columns, 1, null, TRUE));
					$matrix->addRow($sample);
				}			
			}
			
			$index++;
		}
		
		#Attach to the user
		$userRepository = $this->em->getRepository('UserBundle:User');
		$user = $userRepository->find($owner_id);
		$matrix->setOwner($user);
		
		$this->em->persist($matrix);
		$this->em->flush();
		
		return $matrix->getId();
	}

	public function getAllMatrixs(){
		return $this->em->getRepository('MatrixBundle:Matrix')->findAll();
	}
	
	public function getMatrix($id){
		return $this->em->getRepository('MatrixBundle:Matrix')->find($id);
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
	
	public function echoTest(){
		print("Container loaded success");
	}

}

?>
<?php

namespace Ichnaea\WebApp\MatrixBundle\Service;

use Doctrine\ORM\EntityManager;
use Ichnaea\WebApp\MatrixBundle\Entity\Season;
use Ichnaea\WebApp\MatrixBundle\Entity\Variable;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSetComponent;

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
		return $season;
	}
	
	public function getAllSeasons()
	{
		return $this->em->getRepository('MatrixBundle:Season')->findAll();
	}
	
	public function getSeasonById($id)
	{
    	$season = $this->em->getRepository('MatrixBundle:Season')->findById($id);
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
	
	public function createSeasonSet($variable_id, $name, $array_already_seasons = NULL){
		
		$seasonSet = new SeasonSet();
		$seasonSet->setName($name);
		
		$variable = $this->em->getRepository('MatrixBundle:Variable')->find($variable_id);
		$seasonSet->setVariable($variable);
		
		//@TODO: Must add all seasons components
		
		$this->em->persist($seasonSet);
		$this->em->flush();
	}
	/*
	public function createSeasonSet($variable_id, $name, $array_already_seasons = NULL){
		$seasonSet = new SeasonSet();
		$seasonSet->setName($name);
		$seasonSet->setVariableId($variable_id);
		$this->em->persist($seasonSet);
		$this->em->flush();
		$season_set_id = $seasonSet->getId();
		
		//@TODO: mapping relations insert like this?
		foreach($array_already_seasons as $season_id){
			$seasonSetComponent = new SeasonSetComponent();
			$seasonSetComponent->setSeasonSetId($season_set_id);
			$seasonSetComponent->setSeasonId($season_id);
			var_dump($seasonSetComponent);
			$this->em->persist($seasonSetComponent);
			$this->em->flush();
		}
		return $season_set_id;
	}*/
	

	public function getVariableSeasonSets($variable_id){
		return $this->em->getRepository('MatrixBundle:SeasonSet')->findByVariableId($variable_id);
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
			/* When entities well done
			$season = $c->getSeason();
			$components[$index]['name'] = $season->getName();
			$components[$index]['id']   = $season->getId();
			$components[$index]['component_id'] = $c->getId();
			//$season = $c->getSeason();
			//print_r($season->getName());
			$index++;
			*/
		}
		return $components;
	}
	
	public function deleteSeasonSet($seasonSet_id){
		$season = $this->em->getRepository('MatrixBundle:SeasonSet')->find($seasonSet_id);
		$this->em->remove($season);
		$this->em->flush();
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
		
		//$seasonSet_id = $seasonSet->getId();
		//$this->addComponentsToSeasonSet($seasonSet_id, $newComponents, $this->em);
	}
	
	protected function addComponentsToSeasonSet($seasonSet_id, $components, $em)
	{
		var_dump($components);
		foreach($components as $component){
			print("Entering...");
			$seasonSetComponent = new SeasonSetComponent();
			var_dump($seasonSet_id);
			var_dump($component);
			$seasonSetComponent->setSeasonSetId($seasonSet_id);
			$seasonSetComponent->setSeasonId($component);
			var_dump($seasonSetComponent);
			$this->em->persist($seasonSetComponent);
			$this->em->flush();
		}
	
	}
	
	public function deleteSeasonSetComponent($id)
	{
		$seasonSetComponent = $this->em->getRepository('MatrixBundle:SeasonSetComponent')->find($id);
		$this->em->remove($seasonSetComponent);
		$this->em->flush();
	}
	
	public function echoTest(){
		print("Container loaded success");
	}
}

?>
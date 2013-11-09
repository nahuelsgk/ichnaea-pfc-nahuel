<?php 
namespace Ichnaea\WebApp\MatrixBundle\Service;

use Doctrine\ORM\EntityManager;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet as SeasonSet;

/*
 * This service is the responsable of create all seasons files for a matrix 
 */
class IchnaeaMakefileService{
	
	protected $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	/**
	 * @TODO: Remove this awful function
	 * Read all each columns, the season set. Grab all the season and write the season in a txt
	 * Returns the directory path where all the files
	 */
	public function prepareMatrixFiles($matrix_id, $training_id)
	{
		error_log("*** Preparing files\n");
		//Check if the directory exists

		//@TODO: abstract this with configuration files
		$dir_path = '/opt/lampp/htdocs/ichnaea/www/data/'.$training_id;
		if(!file_exists($dir_path)){
			mkdir($dir_path, 0777,  false);
		}
		
		$matrixRepository = $this->em->getRepository('MatrixBundle:Matrix');
		$matrixs = $matrixRepository->find($matrix_id);
		
		$columns = $matrixs->getColumns();
		$i = 0;
		foreach($columns as $column){
			error_log("Column ".$i."\n");
			//Read all the season sets and write it into files
			$seasonSet = $column->getSeasonSet();
			if($seasonSet instanceof SeasonSet){
				$seasons = $seasonSet->getSeason();
			 	foreach($seasons as $season){
			 		$name_file    = $season->getName();
			 		$content_file = $season->getContent();
					error_log("Season: ".$name_file);
					error_log("**Content: ".$content_file."\n");
					$file_path = $dir_path.'/'.$name_file.'.txt';
					error_log("***into ".$file_path);
					file_put_contents($file_path, $content_file);
			 	}
			}
			$i++;
		}
	}
	
}

?>
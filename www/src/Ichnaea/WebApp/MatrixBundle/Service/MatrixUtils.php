<?php 
namespace Ichnaea\WebApp\MatrixBundle\Service;

use Ichnaea\WebApp\MatrixBundle\Entity\Matrix as Matrix;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet as SeasonSet;

/*
 * This service is the responsable of create all seasons files for a matrix 
 */
class MatrixUtils{
	
	public function __construct()
	{
	}
	
	/**
	 * Read all each columns, the season set. Grab all the season and write the season in a txt
	 * Returns the directory path where all the files
	 */
	public function writeMatrixToDisk($matrix)
	{
		error_log("*** Preparing files\n");
		//Check if the directory exists

		//@TODO: abstract this with configuration files
		$dir_path = '/opt/lampp/htdocs/ichnaea/www/data/'.$matrix->getId();
		if(!file_exists($dir_path)){
			mkdir($dir_path, 0777,  false);
		}
		
		
		
		$columns = $matrix->getColumns();
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
	
	/*
	 * Builds an structure for send it to the cue
	 * {
	 *  [dataset_format] => csv
	 *  [aging_format] => tab
	 *  [aging_filename_format] =>  env%column%-%name%.txt
	 *  [aging] => {
	 *    [envBA-Estiu.txt] => season_content
	 *  }
 	 * }
	 * */
    public function buildDatasetFromMatrix($matrix){
    	//init dataset basics
    	$dataSet = array();
    	$dataSet["dataset_format"] = "csv";
    	$dataSet["aging_format"] = "tab";
    	$dataSet["aging_fileformat"] = "env%column%-%name%.txt";
    	$dataSet[aging] = array();
    	
    	//fulfill season contents
    	$columns = $matrix->getColumns();
    	$i = 0;
    	foreach($columns as $column){
    		
    		//get the season set of the column
    		$seasonSet = $column->getSeasonSet();
    		
    		if($seasonSet instanceof SeasonSet){
    			//get the seasons of the season sets
    			$seasons = $seasonSet->getSeason();	
    			
    			//for each of the season...
    			foreach($seasons as $season){
    				$season_name    = $season->getName();
    				$season_content = $season->getContent();
    				$season_season  = resolve_season($season->getStartDate(), $season->getEndDate());
    				error_log("Season: ".$name_file);
    				error_log("**Content: ".$content_file."\n");
    				$file_path = $dir_path.'/'.$name_file.'.txt';
    				error_log("***into ".$file_path);
    				
    			}
    		}
    		$i++;
    	}
    	dumpIntoErrorLog($data);	
    }
    
    private function dumpIntoErrorLog($data){
    	ob_start();
    	var_dump($data);
    	$contents = ob_get_contents();
    	ob_end_clean();
    	error_log($contents);
    }
	
}

?>
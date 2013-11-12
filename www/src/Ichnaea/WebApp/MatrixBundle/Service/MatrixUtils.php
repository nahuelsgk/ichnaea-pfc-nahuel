<?php 
namespace Ichnaea\WebApp\MatrixBundle\Service;

use Ichnaea\WebApp\MatrixBundle\Entity\Matrix as Matrix;
use Ichnaea\WebApp\MatrixBundle\Entity\Variable as Variable;
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
* 
* Return:
* {
*  [dataset_format] => csv
*  [aging_format] => tab
*  [aging_filename_format] =>  env%column%-%name%.txt
*  [aging] => {
*    [envBA-Estiu.txt] => season_content
*    [env/VARIABLE/-/SEASON NAME/] => season_content
*  }
*  [dataset] => "matrix file"
*  
* }
* */
public function buildDatasetFromMatrix($matrix){	
    $dataSet = array();
    $dataSet["fake_duration"]         = 10;
    $dataSet["fake_interval"]         = 1; 
    $dataSet["dataset_format"]        = "csv";
    $dataSet["aging_format"]          = "tab";
    $dataSet["aging_filename_format"] = "env%column%-%name%.txt";
    $dataSet["aging"] = array();
    
    //Array of strings. This is the content of the matrix as a cvs
    $csv_content = array();
    //This array will be the first row of the matrix as csv
    $columns_name = array();
    //...first cell of the first row, default value
    $columns_name[] = "CLASS";
    
    
    //BUILD the aging data
    $i = 0; 
    $columns = $matrix->getColumns();
    foreach($columns as $column){
    	
    	//Column with variable associated
    	if ($column->getVariable() instanceof Variable){
    		$var_id    = $column->getVariable()->getName();
	    	$seasonSet = $column->getSeasonSet();
	    	
	    	if ($seasonSet instanceof SeasonSet){
	    		
	    		$season_set_components = $seasonSet->getComponents();	
	    		$j = 0;
	    		foreach($season_set_components as $component){
	    			$season         = $component->getSeason();
	    			$season_content = $season->getContent();
	    			$season_season  = $component->getSeasonType();
	    			//must respect the "aging_fileformat"
	    			$aging_name = 'env'.$var_id.'-'.$this->resolveSeasonName($season_season).'.txt';
	    			$dataSet["aging"][$aging_name] = $season_content;
	    			$columns_name[] = $var_id;	
	    			$j++;
	    		}
	    	}  
   		}
   		
   		//Another kind of variable, just grab 
   		else{
   			//In this case we use the alias
   			$columns_name[] = $column->getName();
   		}
   		$i++;
   	}
   	
   	//First row, joins all the columns name with ;
   	$csv_content[] = implode($columns_name,";");
   	
   	//Finally ful fill the samples
   	$samples = $matrix->getRows();
   	foreach ($samples as $sample) {
   		$row_content = array();
   		$values      = $sample->getSamples();
   		$sample_name = $sample->getName();
   		$row_content = array_merge( (array) $sample_name, (array)$values);
   		$csv_content[] = implode($row_content, ";");
   	}
   	$csv = implode($csv_content, "\r\n");
   	$dataSet["dataset"] = $csv;
   	
   	//@TODO: by now default values
   	$dataSet['aging_positions'] = array(
   			'Estiu'     => '0.5',
   			'Hivern'    => '0.0',
   			'Summer'    => '0.5',
   			'Winter'    => '0.0'
   	);
   	
	   	
   	return $dataSet;	
}
  

private function resolveSeasonName($season_name){
   	$seasons = array(
   		'summer'   => 'Estiu',
   		'winter'   => 'Hivern',
   		'spring'   => 'Primavera',
   		'autumn'   => 'Tardor',
   		'all_year' => 'TotAny'
  	);
  	return $seasons[$season_name];
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
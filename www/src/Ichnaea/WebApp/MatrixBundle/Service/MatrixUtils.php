<?php 
namespace Ichnaea\WebApp\MatrixBundle\Service;

use Ichnaea\WebApp\MatrixBundle\Entity\Matrix as Matrix;
use Ichnaea\WebApp\MatrixBundle\Entity\Sample as Sample;
use Ichnaea\WebApp\MatrixBundle\Entity\Variable as Variable;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet as SeasonSet;
use Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig as VariableMatrixConfig;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionColumn as PredictionColumn;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample as PredictionSample; 
 
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer as DateTimeToStringTransformer;

/*
 * This service is the responsable of create all seasons files for a matrix 
 */
class MatrixUtils{
	
/*
* Builds a dataSet and metainfo for several usages:
* a) info for send it to the queue
* b) builds a csv file to send for downloading
* 
* IMPORTANT: its an overload function. It's also used for generate csv. 
* The type of csv is simple or completed configured for uploading matrixs
* IMPORTANT: CLASS is a reserved word for empty cells
*
* @param $matrix -> maybe obsolete. It is not used.
* @param string $type: ENUM (simple: just the matrix and its value, complete: NOT 100% IMPLEMENTED)
* @param Collection: Generic Collection of Objects. It depends the objects, use some
* @parar array: Position selected: ONLY for trainings. Array that marks which position must be pushed into samples 
* 
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
*  [dataset] => "matrix file as csv"

*  @TODO: put exemples in documentation
*  
* }
* */
static public function buildDatasetFromMatrix(
		$matrix,
		$type = 'simple',
		\Doctrine\Common\Collections\Collection $columns,
		\Doctrine\Common\Collections\Collection $samples,
		$positions_selected = NULL)
	{	
    $dataSet = array();
    $dataSet["fake_duration"]         = 10;
    $dataSet["fake_interval"]         = 1; 
    $dataSet["dataset_format"]        = "csv";
    $dataSet["aging_format"]          = "tab";
    $dataSet["aging_filename_format"] = "env%column%-%aging%.txt";
    $dataSet["aging"] = array();
    
    $empty_cell_string = "CLASS";
    
    //Array of strings. This is the content of the matrix as a cvs
    $csv_content = array();
    //This array will be the first row of the matrix as csv
    $columns_name = array();
    //...first cell of the first row, default value
    $columns_name[] = $empty_cell_string;

    //Empty cells if complete
    if ($type == 'complete') {
    	$columns_name[] = $empty_cell_string;
    	$columns_name[] = $empty_cell_string;
    }   
    
    //BUILD the aging data
    $i = 0; 
    //@GENERIC $columns = $matrix->getColumns();
    
    
    //SPECIAL Variable: Just the remember which positions in the prediction Matrix
    //Array [position] => trained 
    $trainedPositionsInPredictionMatrix = array();
    //echo "<pre>";
    //BEGIN CSV @1 - ROWS with the var name/alias if simple; if complete only var name
    foreach($columns as $column)
    {
    	//For Trainings
    	if($column instanceof VariableMatrixConfig)
    	{	
    	    //Column with variable associated
	    	if ($column->getVariable() instanceof Variable)
	    	{
	    		$var_name    = $column->getVariable()->getName();
		    	$seasonSet = $column->getSeasonSet();
		    	
		    	if ($seasonSet instanceof SeasonSet){
		    		
		    		$season_set_components = $seasonSet->getComponents();	
		    		$j = 0;
		    		foreach($season_set_components as $component){
		    			$season         = $component->getSeason();
		    			$season_content = $season->getContent();
		    			$season_season  = $component->getSeasonType();
		    			//must respect the "aging_fileformat"
		    			$aging_name = 'env'.$var_name.'-'.MatrixUtils::resolveSeasonName($season_season).'.txt';
		    			$dataSet["aging"][$aging_name] = $season_content;
		    			$j++;
		    		}
		    	}  
		    	//Just one column per variable
		    	$columns_name[] = $var_name;
	    	}
	    	//Another kind of column(no variable associated)
	    	else{
	    		//In this case we use the alias
	    		if ($type == 'simple') $columns_name[] = $column->getName();
	    		else $columns_name[] = $empty_cell_string;
	    	}
   		}
   		
   		//For Predictions
   		elseif($column instanceof PredictionColumn)
   		{	
   			//Only with a configuration Column with trained variables
   			if(!is_null($column->getColumnConfiguration())){
   				//echo "COLUMN FOR PREDICTION in position ".$column->getIndex()."<br>";
   				$trainedPositionsInPredictionMatrix[$column->getIndex()] = 'trained';
   				
	   			//echo "* IS CONFIGURED<br>";
	   			//Column with a trained variable with season set
	   			if ($column->getColumnConfiguration()->getVariable() instanceof Variable){
	   				//echo "**VARIABLE WITH SEASON SET";
	   				$var_name    = $column->getColumnConfiguration()->getVariable()->getName();
	   				$seasonSet = $column->getColumnConfiguration()->getVariable()->getSeasonSet();
	   				 
	   				if ($seasonSet instanceof SeasonSet){
	   			
	   					$season_set_components = $seasonSet->getComponents();
	   					$j = 0;
	   					foreach($season_set_components as $component){
	   						$season         = $component->getSeason();
	   						$season_content = $season->getContent();
	   						$season_season  = $component->getSeasonType();
	   						//must respect the "aging_fileformat"
	   						$aging_name = 'env'.$var_name.'-'.MatrixUtils::resolveSeasonName($season_season).'.txt';
	   						$dataSet["aging"][$aging_name] = $season_content;
	   						$j++;
	   					}
	   				}
	   				//Just one column per variable
	   				$columns_name[] = $var_name;
	   			}
	   			//Another kind of column: column with a trained variable "derivated"(no seasonset)
	   			else
	   			{
	   				//echo "** VARIABLE DERIVATED WITHOUT SEASON SET";
	   				//In this case we use the alias
	   				if ($type == 'simple') $columns_name[] = $column->getColumnConfiguration()->getName();
	   				else $columns_name[] = $empty_cell_string;
	   			}
   			}
   		}
   		
   		
   		
   		$i++;
   	}	
   	//echo "<br>COLUMNS TRAINED";
   	//var_dump($trainedPositionsInPredictionMatrix);
   	
   	//First row, joins all the columns name with ;
   	$csv_content[] = implode($columns_name,";");
   	//END CSV @1
   	
   	//***************************
   	//* BEGIN ONLY FOR COMPLETE *
   	//***************************
   	//BEGIN CSV @2 - Row with id of the season set
   	$columns_name = array();
   	if ($type == 'complete')
   	{
   		$columns_name = array();
   		$columns_name[] = $empty_cell_string;
   		$columns_name[] = $empty_cell_string;
   		$columns_name[] = $empty_cell_string;
   		foreach($columns as $column)
   		{
   			if ($column->getVariable() instanceof Variable)
   			{
   				$columns_name[] = $column->getSeasonSet()->getId();
   			}
   			else $columns_name[] = $empty_cell_string;		 
   		}
   		$csv_content[] = implode($columns_name,";");
   	}
   	//END CSV @2
   	
   	//BEGIN CSV @3 - Row with the aliases
   	$columns_name = array();
   	if ($type == 'complete')
   	{
   		$columns_name = array();
   		$columns_name[] = $empty_cell_string;
   		$columns_name[] = $empty_cell_string;
   		$columns_name[] = $empty_cell_string;
   		foreach($columns as $column)
   		{
   		    $columns_name[] = $column->getName();
   		}
   		$csv_content[] = implode($columns_name,";");
   	}
   	//END CSV @3
   	//***************************
   	//* END   ONLY FOR COMPLETE *
   	//***************************
   	
   	//Finally ful fill the samples
   	//@GENERIC $samples = $matrix->getRows();
   	$row_content = array();
   	foreach ($samples as $sample) {
   		//echo "SAMPLE<br>";
   		$row_content   = array();
   		$values        = $sample->getSamples();
   		$sample_name   = $sample->getName();
   		$sample_date   = $sample->getDate();
   		$sample_origin = $sample->getOrigin(); 
   		if ($type == 'simple')
   		{
   			
   			//echo "* CLASS: ".get_class($sample)."<br>";
   			//echo "* Sample name: ".$sample->getName()."<br>";
   			//\Doctrine\Common\Util\Debug::dump($sample);
   			//For predictions only the configurated columns
   			if (get_class($sample) === 'Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample')
   			{
   				//echo "* SAMPLE FROM PREDICTION<br>";
   				$current_position = 1;
   				foreach($sample->getSamples() as $sample_value)
   				{
   					//var_dump($sample_value);
   					//current_position - 1 because the array is shifted one position
   					if (array_key_exists($current_position, $trainedPositionsInPredictionMatrix)){
   						//echo "** POSITION $current_position is SELECTED THE VALUE ".$sample_value."<br>";
   						$row_content[] = $sample_value;
   					}
   					$current_position++;	
   				}
   			}
   			//For trainings complete matrix
   			elseif (get_class($sample) == 'Ichnaea\WebApp\MatrixBundle\Entity\Sample')
   			{
   				//if is null(no param passed), push all values(by now only used by download as csv)
   				if(is_null($positions_selected)) $row_content = array_merge( (array) $sample_name, (array)$values);
   				//for create trainings
	   			else{
	   				$current_position=1;
	   				$row_content_sample = array();
	   				foreach($sample->getSamples() as $sample_value){
	   					if(array_key_exists($current_position, $positions_selected)){
	   						$row_content_sample[] = $sample_value;
	   					}
	   					$current_position++;
	   					$row_content = array_merge( (array) $sample_name, (array)$row_content_sample);
	   				}
   				}
   				
   			}	
   			else
   			{
   				//echo "* PROBLEMS!!";	
   			}  			
   			
   		}
   		//BEGIN CSV @4 - IF complete add origins and dates
   		else
   		{ 
   			$row_content = array_merge(
   					(array) $sample_name, 
   					$sample_date instanceof \DateTime ? (array) $sample_date->format('Y-m-d') : (array) $empty_cell_string, 
   					!empty($sample_origin) ? (array) $sample_origin : (array) $empty_cell_string,
   					(array) $values
   			);
   		}
   		//echo "VALUES PUSHED<br>";
   		//var_dump($row_content);
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
   	//echo "<pre>";
   	//var_dump($dataSet);
   	//echo "</pre>";
	//die();   	
   	return $dataSet;	
}

/**
 * 
 * @param unknown $matrix
 * @param string $type
 * @return multitype:multitype: number string multitype:string
 */
static public function buildDatasetFromMatrixPrediction($matrix, $type = 'simple'){
	$dataSet = array();
	$dataSet["fake_duration"]         = 10;
	$dataSet["fake_interval"]         = 1;
	$dataSet["dataset_format"]        = "csv";
	$dataSet["aging_format"]          = "tab";
	$dataSet["aging_filename_format"] = "env%column%-%aging%.txt";
	$dataSet["aging"] = array();

	$empty_cell_string = "CLASS";

	//Array of strings. This is the content of the matrix as a cvs
	$csv_content = array();
	//This array will be the first row of the matrix as csv
	$columns_name = array();
	//...first cell of the first row, default value
	$columns_name[] = $empty_cell_string;

	//Empty cells if complete
	if ($type == 'complete') {
		$columns_name[] = $empty_cell_string;
		$columns_name[] = $empty_cell_string;
	}

	//BUILD the aging data
	$i = 0;
	//TODO: generic
	$columns = $matrix->getTraining()->getMatrix()->getColumns();

	//BEGIN CSV @1 - ROWS with the var name/alias if simple; if complete only var name
	foreach($columns as $column){
		//Column with variable associated
		if ($column->getVariable() instanceof Variable){
			$var_name    = $column->getVariable()->getName();
			$seasonSet = $column->getSeasonSet();

			if ($seasonSet instanceof SeasonSet){
				 
				$season_set_components = $seasonSet->getComponents();
				$j = 0;
				foreach($season_set_components as $component){
					$season         = $component->getSeason();
					$season_content = $season->getContent();
					$season_season  = $component->getSeasonType();
					//must respect the "aging_fileformat"
					$aging_name = 'env'.$var_name.'-'.MatrixUtils::resolveSeasonName($season_season).'.txt';
					$dataSet["aging"][$aging_name] = $season_content;
					$j++;
				}
			}
			//Just one column per variable
			$columns_name[] = $var_name;
		}
		 
		//Another kind of column(no variable associated)
		else{
			//In this case we use the alias
			if ($type == 'simple') $columns_name[] = $column->getName();
			else $columns_name[] = $empty_cell_string;
		}
		$i++;
	}
	//First row, joins all the columns name with ;
	$csv_content[] = implode($columns_name,";");
	//END CSV @1

	//BEGIN CSV @2 - Row with id of the season set
	$columns_name = array();
	if ($type == 'complete')
	{
		$columns_name = array();
		$columns_name[] = $empty_cell_string;
		$columns_name[] = $empty_cell_string;
		$columns_name[] = $empty_cell_string;
		foreach($columns as $column)
		{
			if ($column->getVariable() instanceof Variable)
			{
				$columns_name[] = $column->getSeasonSet()->getId();
			}
			else $columns_name[] = $empty_cell_string;
		}
		$csv_content[] = implode($columns_name,";");
	}
	//END CSV @2

	//BEGIN CSV @3 - Row with the aliases
	$columns_name = array();
	if ($type == 'complete')
	{
		$columns_name = array();
		$columns_name[] = $empty_cell_string;
		$columns_name[] = $empty_cell_string;
		$columns_name[] = $empty_cell_string;
		foreach($columns as $column)
		{
			$columns_name[] = $column->getName();
		}
		$csv_content[] = implode($columns_name,";");
	}
	//END CSV @3

	//Finally ful fill the samples
	$samples = $matrix->getRows();
	foreach ($samples as $sample) {
		$row_content   = array();
		$values        = $sample->getSamples();
		$sample_name   = $sample->getName();
		$sample_date   = $sample->getDate();
		$sample_origin = $sample->getOrigin();
		if ($type == 'simple') $row_content = array_merge( (array) $sample_name, (array)$values);
		 
		//BEGIN CSV @4 - IF complete add origins and dates
		else
		{
			$row_content = array_merge(
					(array) $sample_name,
					$sample_date instanceof \DateTime ? (array) $sample_date->format('Y-m-d') : (array) $empty_cell_string,
					!empty($sample_origin) ? (array) $sample_origin : (array) $empty_cell_string,
					(array) $values
			);
		}
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

static private function resolveSeasonName($season_name){
   	$seasons = array(
   		'summer'   => 'Estiu',
   		'winter'   => 'Hivern',
   		'spring'   => 'Primavera',
   		'autumn'   => 'Tardor',
   		'all_year' => 'TotAny'
  	);
  	return $seasons[$season_name];
}

static public function resolveOriginInSampleName($sample_name){
	$ret = '';
	if(strpos($sample_name, 'HM')>0) return 'HUMAN';
	elseif (strpos($sample_name, 'PG')>0) return 'PIG';
	elseif (strpos($sample_name, 'CW')>0) return 'COW';
	elseif (strpos($sample_name, 'PL')>0) return 'POULTRY';
	return $ret;
}

/**
 * Cleans string 
 * 
 * @param string $string
 * @return mixed
 */
static public function cleanStringCSV($string){
	$invalid_chars = array('\'','"');
	return str_replace($invalid_chars, "", $string);
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
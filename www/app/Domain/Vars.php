<?php 
namespace Domain;

use \DBi;

class Vars{
  private static $TABLE = 'var';
  private static $FIELDS = array ('id', 'name', 'threshold' ,'created');

  public function __construct(){
  
  }

  /*
  * Saves a Var into the Database
  * - $vars: array of associative arrays
  *   ["name"] => name_of_the_var
  *   ["threshold"] => threshold_of_the_var
  *   ["mid"] => automatic agregates that var to matrix
  * 
  * Returns an array of ["name"] => id
  */
  public function create($vars){
    $db = new DBi();
    
    $db->TransactionBegin();
    $ret = array();
    try{
      foreach ($vars as $var){
        $st = "INSERT INTO ".self::$TABLE."(name, threshold) VALUES (" . DBi::SQLValue($var["name"], DBi::SQLVALUE_TEXT) . "," . DBi::SQLValue($var["threshold"]) . ")";
        $db->Query($st);
        if($db->Error()) throw new \Exception("Error inserting the variable: $st");
	$vid = $db->GetLastInsertId();
        
	$mid = '';
	if(isset($var["mid"])){
	  $mid = $var["mid"];
	  $this->associatesVarToMatrix($vid, $mid, $db);
	}
	array_push($ret,array("name" => $var["name"], "threshold" => $var["threshold"], "vid" => $vid, "mid" => $mid));
      }
    }
    catch(\Exception $e){
      printHTML($e->getMessage());
      $db->TransactionEnd();
    }
    $db->TransactionEnd();
    return $ret;
  }
  
  /*
  * Adds a var into a matrix.
  * Can be called as NameSpace function.
  *
  * 1st April 2013
  */
  public function associatesVarToMatrix($vid, $mid, $db = NULL){
    if (!isset($db)) $db = new DBi();
    
    $insert = "INSERT INTO matrix_vars(var_id, matrix_id) VALUES($vid, $mid)";
    $db->Query($insert);
    if($db->Error()) throw new \Exception("Error associating the varible to the matrix: $insert");
    return $db->GetLastInsertId();
  }

  /*
  * Gets all the vars definition from a matrix
  * Returns an array of objects
  */
  public function getVarsFromMatrixs($mid){
    $db = new DBi();
    $vars =  $db->QuerySimple("SELECT v.* FROM var v INNER JOIN matrix_vars mv ON v.id = mv.var_id WHERE mv.matrix_id = '%d'",array($mid), "execute_return");
    return $vars;
  }
}
?>

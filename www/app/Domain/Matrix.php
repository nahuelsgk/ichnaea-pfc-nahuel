<?php

class Matrix{
  
  private static $FIELDS = array(
    'id'         => DBi::SQLVALUE_NUMBER, 
    'name'       => DBi::SQLVALUE_TEXT, 
    'active'     => DBi::SQLVALUE_Y_N, 
    'public' 	 => DBi::SQLVALUE_Y_N,
    'status'	 => DBi::SQLVALUE_TEXT,
    'creator'	 => DBi::SQLVALUE_NUMBER,
    'created'	 => DBi::SQLVALUE_DATETIME);
  private static $TABLE = 'matrix';

  private $name = '';
  private $project_id = '';
  private $matrix_id = '';
  private $sample = array();
  private $vars = array();
  private $matrix_building=array(array());


  public function __construct(){
  }
  
  /*
  * Build basic info object
  *
  * Last update:
  */
  public function initMatrix($mid){
      
  }

  /*
  * Saves a matrix with a status 'new'
  */
  public function saveEmptyMatrix(){
    $db = new DBi();  
    $string = $db->QuerySimple("INSERT INTO matrix(status) VALUES ('new')",NULL,"query");
  }

  /*
  * NAMESPACES functions
  */

  /*
  * Namespace function: Returns a list of matrixs
  *  - $fields(opt): select some columns, if NULL select all
  *  - $params:
  *    - "only_public"
  *  Last update: 12 march 2013
  */
  public function listMatrixs($fields = NULL, $params = NULL){
   $db = new DBi();
 
   if (isset($params["only_public"]) && $params["only_public"]){
     $where["public"] = 'y';
   }
   
   $matrixs = $db->BuildSQLSelectGeneric("execute_local_catch", self::$TABLE, self::$FIELDS, $where );
   return $matrixs;
   }
}
?>

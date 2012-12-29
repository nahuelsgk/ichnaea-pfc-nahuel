<?php

includeLib("Domain/Vars");

class Matrix{
  
  private static $FIELDS = array('id', 'project_id', 'name', 'created');
  private static $TABLE = 'matrix';

  private $name;
  private $vars;
  private $project_id;

  public function __construct(array $attributes){
    $this->name       = DBi::SQLValue($attributes['name']);
    $this->vars       = $attributes['vars'];
    $this->project_id = DBi::SQLValue($attributes['project_id'], DBi::SQLVALUE_NUMBER);
  }

  public function saveMatrix(){
    $db = new DBi();
    
    //TODO: ROLLBACK if any error//
    //Save the values of the matrix
    $values = array();
    foreach (array('name','project_id') as $v) { $values[$v] = $this->$v; }
    $st = $db->BuildSQLInsert(
      self::$TABLE,
      $values
    );
    $db->Query($st);

    //Save the metrics
    Vars::saveProjectVars($this->project_id,$this->vars);
    }
    //TODO: ROLLBACK if any error//

  /*
  * NAMESPACE FUNCTION
  */

  /*
  * getMatrixsFromProject
  *
  * Returns the matrix from the project $project_id
  */
  public function  getMatrixsFromProject($project_id){
     $db = new DBi();
     $st = $db->BuildSQLSelect(
       self::$TABLE,
       array("project_id"=>$project_id),
       self::$FIELDS
     );
     $matrixs = $db->QueryArray($st);
     return $matrixs;
  }
  
}

?>

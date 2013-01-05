<?php

//includeLib("Domain/Vars");

class Matrix{
  
  private static $FIELDS = array('id', 'project_id', 'name', 'active', 'created');
  private static $TABLE = 'matrix';

  private $name;
  private $vars;
  private $project_id;
  private $matrix_id;

  public function __construct(array $attributes){
    $this->name       = DBi::SQLValue($attributes['name']);
    $this->vars       = $attributes['vars'];
    $this->project_id = DBi::SQLValue($attributes['project_id'], DBi::SQLVALUE_NUMBER);
  }

  public function saveMatrix(){
    $db = new DBi();
    
    //Save the values of the matrix
    $values = array();
    foreach (array('name','project_id') as $v) { $values[$v] = $this->$v; }
    $st = $db->BuildSQLInsert(
      self::$TABLE,
      $values
    );

    try{
      $db->TransactionBegin(); 
      $db->Query($st);
    
      //Save the metrics
      Vars::saveMatrixVars($db->GetLastInsertId(), $this->vars);
      $db->TransactionEnd();
    }
    catch(Exception $e) {
       $db->TransactionRollback();
       printHTML($e->getMessage()); 
    }
  }


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
       array("project_id"=>$project_id, "active"=>DBi::SQLValue("y",DBi::SQLVALUE_Y_N )),
       self::$FIELDS
     );
     $matrixs = $db->QueryArray($st);
     return $matrixs;
  }

  /*
  * Disable a set of matrixs
  */
  public function disableMatrix($set_ids){
    $db = new DBi();
    $st = "UPDATE ".self::$TABLE." SET active='n' WHERE id IN (".implode(',', $set_ids).")";
    printHTML($st);
    $db->Query($st);
    printHTML($db->Error());
  }


  /*
  * Returns all the matrixs from the user with id $user_id as an array of associative arrays.
  */
  public function getUserMatrixs($id){
    $db = new Dbi();
    
    $sl = "SELECT m.name as name_matrix, m.id as id_matrix, p.name as name_project, p.id as id_project
    FROM matrix m INNER JOIN projects p ON m.project_id = p.id 
    WHERE p.user_owner = '$id'";
    return $db->QueryArray($sl);
  }

  /*
  * Returns the variables of the matrix with the id
  */
  public function getMatrixVars($mid){
    $db = new DBi();

    $sl = "SELECT v.name, v.id
    FROM matrix m INNER JOIN var v ON v.matrix_id = m.id
    WHERE v.matrix_id = '$mid'";
    
    $vars = $db->QueryArray($sl, MYSQL_ASSOC);
    printHTML($db->Error());
    return $vars;
  }
  public function getMatrixSamples($mid){
     $db = new DBi();

    $sl = "SELECT s.id 
    FROM sample s INNER JOIN matrix m ON s.matrix_id = m.id WHERE s.matrix_id =  $mid";
    $rows =  $db->QueryArray($sl,MYSQL_ASSOC);
    printHTML($db->Error());
    return $rows;
  }
  public function getMatrixSamplesValues($mid){
    $db = new DBi();

    $sl = "SELECT v.sample_id, v.var_id, v.value  
    FROM value v INNER JOIN sample s ON s.id = v.sample_id WHERE s.matrix_id =  $mid";
    $values =  $db->QueryArray($sl,MYSQL_ASSOC);
    printHTML($db->Error());
    return $values;
  }
  /*
  * Values: Actually an array of StdObject with this attributes:
  * - id -> Id of the var
  * - value
  */
  public function saveNewSample($mid, $values){
    printHTML("SAVING NEW SAMPLE"); 
    Sample::saveSample($mid,$values);
  }
}

class Sample{
  private static $FIELDS = array('id', 'matrix_id', 'created');
  private static $TABLE = 'sample';

  public function __construct(){}
    
  public function saveSample($mid, $values){
    printHTML('saving row');
    $db = new DBi();
    $st = $db->BuildSQLInsert(
          self::$TABLE,
	  array('matrix_id' => DBi::SQLValue($mid))
	  );
    printHTML($st);
    try{
    $db->TransactionBegin();
    $db->Query($st);
    $sample_id = $db->GetLastInsertID();
    Values::saveValues($sample_id,$values, $db);
    $db->TransactionEnd();
    }
    catch(Exception $e){
      $db->TransactionRollback();
      printVar($e);
    }
  }


}

class Values{
  private static $TABLE = "value";
  private static $FIELDS = array('id', 'sample_id', 'var_id', 'value', 'created');

  public function __construct(){}

  public function saveValues($sample_id, $values, $db){
    if(!isset($db)) $db = new DBi();
    $st = "INSERT INTO ".self::$TABLE."(sample_id, var_id, value ) VALUES ";
    foreach ($values as $value){
      $st_val[] = '(' . DBi::SQLValue($sample_id, DBi::SQLVALUE_NUMBER) . ',' . DBi::SQLValue($value['id'], DBi::SQLVALUE_NUMBER) .','. DBi::SQLValue($value['value'], DBi::SQLVALUE_NUMBER) .')';
    }
    $st.=implode(',',$st_val);
    printHTML($st);
    if ($db->Query($st) == FALSE) throw new Exception($db->Error());

  }
}

class Vars{
  private static $TABLE = 'var';
  private static $FIELDS = array ('id', 'matrix_id', 'name', 'created');

  public function __construct(){}

  public function saveMatrixVars($pid,$vars){
    $db = new Dbi();

    $st = "INSERT INTO ".self::$TABLE."(matrix_id,name) VALUES ";
    foreach ($vars as $var){
      $values[] = '(' . DBi::SQLValue($pid, DBi::SQLVALUE_NUMBER) . ',' . DBi::SQLValue($var) . ')';
    }
    $st.=implode(',', $values);
    printHTML($st);
    $db->Query($st);
    }
  }
?>

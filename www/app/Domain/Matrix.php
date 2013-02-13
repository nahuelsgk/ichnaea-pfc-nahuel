<?php

class Matrix{
  
  private static $FIELDS = array(
    'id'         => DBi::SQLVALUE_NUMBER, 
    'project_id' => DBi::SQLVALUE_NUMBER, 
    'name'       => DBi::SQLVALUE_TEXT, 
    'active'     => DBi::SQLVALUE_Y_N, 
    'created'	 => DBi::SQLVALUE_DATETIME);
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

  /*
  * Save the matrix to the DB. Object metrod
  */
  public function saveMatrix(){
    $db = new DBi();
    
    //Prepare the matrix sql to insert
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
  * NAMESPACE FUNCTIONS
  */
  
  /*
  * Namespace function to save a new matrix
  */
  public function saveNewMatrix($pid, $name, $variables = NULL){
    $db = new DBi();
    //Prepare the matrix sql to insert
    $values['name']       = DBi::SQLValue($name);
    $values['project_id'] = DBi::SQLValue($pid, DBi::SQLVALUE_NUMBER);
    $st = $db->BuildSQLInsert(
      self::$TABLE,
      $values
    );
    try{
      $db->TransactionBegin();
      $db->Query($st);
      $mid = $db->GetLastInsertId();
      Vars::saveMatrixVars2($mid, $variables, $db);
      $db->TransactionEnd();
      return $mid;
    }
    catch(Exception $e){
      $db->TransactionRollback();
      printHTML($e->getMessage());
    }
  }
  
  /*
  * Namespace function to add variables definition to a matrix
  */
  public function saveNewVariables($mid, $variables){
      Vars::saveMatrixVars2($mid, $variables);
  }

  /*
  * Get all the info from a matrix
  * - $params(optional): array with the not all the columns columns
  */
  public function getMatrixDefinition($mid, $params = NULL){
    $db = new DBi();

    $st = $db->BuildSQLSelect(
      self::$TABLE,
      array("id" => DBi::SQLValue($mid, DBi::SQLVALUE_NUMBER)),
      isset($params) ? $params : array_keys(self::$FIELDS)
    );
    
    if (($matrix = $db->QuerySingleRowArray($st, MYSQL_ASSOC))===FALSE) throw new Exception($db->Error);
    
    return $matrix;
  }

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
     $matrixs = $db->Query($st);
     return $matrixs;
  }

  /*
  * Disable a set of matrixs
  */
  public function disableMatrix($set_ids){
    $db = new DBi();
    $st = "UPDATE ".self::$TABLE." SET active='n' WHERE id IN (".implode(',', $set_ids).")";
    $db->Query($st);
    printHTML($db->Error());
  }


  /*
  * Returns matrix's basic info from a user_id.
  * - $pid: if $pid defined will return the from a concrete project.
  */
  public function getUserMatrixs($id, $pid = NULL){
    $db = new Dbi();
    $sl = "SELECT m.name as name_matrix, m.id as id_matrix, p.name as name_project, p.id as id_project
    	FROM matrix m INNER JOIN projects p ON m.project_id = p.id 
    	WHERE p.user_owner = '$id' AND
	m.active = 'y'";
    if (isset ($pid)) $sl.=" AND p.id = '$pid'";
    return $db->QueryArray($sl);
  }

  /*
  * Returns array of the variables of the matrix
  */
  public function getMatrixVars($mid){
    $db = new DBi();

    $sl = "SELECT v.name, v.id, v.threshold_limit
    FROM matrix m INNER JOIN var v ON v.matrix_id = m.id
    WHERE v.matrix_id = '$mid'
    ORDER BY v.id";
    
    $vars = $db->QueryArray($sl, MYSQL_ASSOC);
    printHTML($db->Error());
    return $vars;
  }

  /*
  * Returns an associative array with the id of the samples
  */
  public function getMatrixSamples($mid){
     $db = new DBi();

    $sl = "SELECT s.id 
    FROM sample s INNER JOIN matrix m ON s.matrix_id = m.id WHERE s.matrix_id =  $mid";
    $rows =  $db->QueryArray($sl,MYSQL_ASSOC);
    printHTML($db->Error());
    return $rows;
  }

  /*
  * Returns the values from a matrix
  */
  public function getMatrixSamplesValues($mid){
    $db = new DBi();

    $sl = "SELECT v.sample_id, v.var_id, v.value  
    FROM value v INNER JOIN sample s ON s.id = v.sample_id WHERE s.matrix_id =  $mid";
    $values =  $db->QueryArray($sl,MYSQL_ASSOC);
    printHTML($db->Error());
    return $values;
  }

  /*
  * Returns a complete matrix values. Even if the the MxN value doesn't exist
  * Returns:
  * [sample_id][var_id] = array(id=>value_id, value=> its_value)
  */
  public function getMatrixSamplesValuesComplete($mid){
    $db = new DBi();
    $sl = "SELECT complete_matrix.sample_id, variable_id, variable_name, v.id, v.value 
          FROM (
	      SELECT s.id sample_id, v.id variable_id, v.name variable_name 
	      FROM sample s,var v  WHERE s.matrix_id = $mid AND v.matrix_id=$mid
	    ) as complete_matrix 
	    LEFT JOIN value v ON v.sample_id = complete_matrix.sample_id AND v.var_id = complete_matrix.variable_id
	    ORDER BY complete_matrix.sample_id, variable_id ";
    if (($values = $db->QueryArray($sl,MYSQL_ASSOC))===FALSE) throw new Exception($db->Error());
    $matrix = array();
    foreach($values as $v){
      $matrix[$v["sample_id"]][$v["variable_id"]] = array("id"=>$v["id"], "value"=>$v["value"]);
    }
    return $matrix;
  }
  /*
  * Calls a function to save a sample.
  */
  public function saveNewSample($mid, $values){
    return Sample::saveSample($mid,$values);
  }
  
  /*
  * Delete a sample from a matrix
  */
  public function deleteSample($sample_id){
    return Sample::deleteSample($sample_id);
  }

  /*
  * Perfoms a generic update definition.
  * -params: array of associative array. Each key references the field and the values set the new updated values
  */
  public function updateDefinition($mid, $params){
    $db = new DBi();
    foreach($params as $k=>$v){
          $params[$k] = DBi::SQLValue($v, self::$FIELDS[$k]);
    }

    $upd = $db->BuildSQLUpdate(self::$TABLE, $params, array('id' => $mid));
    if ($db->Query($upd) === FALSE) throw new Exception($db->Error());
  }
}

class Sample{
  private static $FIELDS = array('id', 'matrix_id', 'created');
  private static $TABLE = 'sample';

  public function __construct(){}
    
  public function saveSample($mid, $values){
    $db = new DBi();
    $st = $db->BuildSQLInsert(
          self::$TABLE,
	  array('matrix_id' => DBi::SQLValue($mid))
	  );
    //Transaction: saving few values
    try{
      $db->TransactionBegin();
      
      //First save the sample
      $db->Query($st);
      $sample_id = $db->GetLastInsertID();

      //Save the array of values
      Values::saveValues($sample_id, $values,$db) ;
      $db->TransactionEnd();
    }
    catch(Exception $e){
      $db->TransactionRollback();
      printVar($e);
      return FALSE;
    }
  return;
  }

  /*
  * Delete a sample and its values
  */
  public function deleteSample($sample_id){
    $db = new DBi();
    $st = $db->BuildSQLDelete(
      self::$TABLE,
      array('id' => DBi::SQLValue($sample_id))
    );

    //Transaction: delete all values from the sample $sample_id
    try{
      $db->TransactionBegin();
      
      //First delete the sample to respect the foreign keys
      Values::deleteValuesFromSample($sample_id,$db);

      //Second delete the referenced sample
      $db->Query($st);
      $db->TransactionEnd();
    }
    catch(Exception $e){
      $db->TransactionRollback();
      printVar($e);
      return FALSE;
    }
  }
 
}

class Values{
  private static $TABLE = "value";
  private static $FIELDS = array(
  	'id' => DBi::SQLVALUE_NUMBER, 
	'sample_id' => DBi::SQLVALUE_NUMBER, 
	'var_id' => DBi::SQLVALUE_NUMBER,
	'value' => DBi::SQLVALUE_NUMBER,
	'created' => DBi::SQLVALUE_DATETIME);

  public function __construct(){}

  /*
  * Save the values to sample.
  * - values: associative id:
       [id] => variable_id
       [value] => value
  */
  public function saveValues($sample_id, $values, $db = NULL){
    if(empty($values)) return;
  
    if(!isset($db)) $db = new DBi();
    
    $st = "INSERT INTO ".self::$TABLE."(sample_id, var_id, value ) VALUES ";
    foreach ($values as $value){
      $st_val[] = '(' . DBi::SQLValue($sample_id, DBi::SQLVALUE_NUMBER) . ',' . DBi::SQLValue($value['id'], DBi::SQLVALUE_NUMBER) .','. DBi::SQLValue($value['value'], DBi::SQLVALUE_NUMBER) .')';
    }
    $st.=implode(',',$st_val);
    if ($db->Query($st) == FALSE) throw new Exception($db->Error());

  }

  public function deleteValuesFromSample($sample_id, $db){
    if(!isset($db)) $db = new DBi();
    $st = $db->BuildSQLDelete(
      self::$TABLE,
      array('sample_id' => DBi::SQLValue($sample_id))
    );
    printHTML("Deleting values with the query ".$st);
    if ($db->Query($st) == FALSE) throw new Exception($db->Error());
  }

  public function updateValue($value_id, $value){
    $db = new DBi();
    $upd = $db->BuildSQLUpdate(
      self::$TABLE, 
      array("value" => DBi::SQLValue($value, self::$FIELDS["value"])),
      array('id' => $value_id)
    );
    if ($db->Query($upd) == FALSE) throw new Exception($db->Error());

  }
}

class Vars{
  private static $TABLE = 'var';
  private static $FIELDS = array (
  	'id'=>DBi::SQLVALUE_NUMBER, 
	'matrix_id'=> DBi::SQLVALUE_NUMBER,
	'name' => DBi::SQLVALUE_TEXT,
	'threshold_limit' => DBi::SQLVALUE_NUMBER, 
	'created' => DBi::SQLVALUE_DATETIME);

  public function __construct(){}
  
  public function saveMatrixVars($pid,$vars, $db){
    if (!isset($db)) $db = new Dbi();

    $st = "INSERT INTO ".self::$TABLE."(matrix_id, name, threshold_limit ) VALUES ";
    foreach ($vars as $var){
      printVar($var);
      $values[] = '(' . DBi::SQLValue($pid, DBi::SQLVALUE_NUMBER) . ',' . DBi::SQLValue($var) . ')';
    }
    $st.=implode(',', $values);
    printHTML($st);
    $db->Query($st);
  }
  
  public function updateVar($update, $db = NULL){
    if (!isset($db)) $db = new DBi();
    
    //Get the id and remove it 
    $id = DBi::SQLValue($update['id'], DBi::SQLVALUE_NUMBER);

    unset($update['id']);
    unset($update['action']);
    foreach($update as $k=>$v){
      $values[$k] = DBi::SQLValue($v, self::$FIELDS[$k]);
    }
    $st = $db::BuildSQLUpdate(self::$TABLE, $values, array('id' => $id));
    if( $db->Query($st) === FALSE ) throw new Exception($db->Error());
  }
  /*
  * Returns all the fields from a var
  */
  public function getVarsDefinition($mid){
    $db = new DBi();
    $st = $db->BuildSQLSelect(self::$TABLE, 
    	  array("matrix_id" => DBi::SQLValue($mid, DBi::SQLVALUE_NUMBER)), 
	  array_keys(self::$FIELDS)
	);
    if (($vars = $db->QueryArray($st, MYSQL_ASSOC)) === FALSE ) throw new Exception($db->Error());
    return $vars;
  }

  /*
  * Function to save new matrixVars. $vars is an array of associatives array
  */
  public function saveMatrixVars2($pid, $vars, $db = NULL){
 
    if ($vars == NULL) return;
    if ($db == NULL) $db = new DBi();
    
    //Check if the variables is an array of array of associatives
    if (!isset($vars[0]["name"])) throw new Exception("Variables structure bad formed");

    //Build a big insert
    $st = "INSERT INTO ".self::$TABLE."(matrix_id, name, threshold_limit ) VALUES ";
    foreach($vars as $var){
      $values[] = '(' . DBi::SQLValue($pid,  DBi::SQLVALUE_TEXT). ',' .
        DBi::SQLValue($var["name"], DBi::SQLVALUE_TEXT) . ',' . 
      	DBi::SQLValue($var["threshold_limit"],  DBi::SQLVALUE_NUMBER) . ')';
    }
    $st.=implode(',', $values);
    
    if ( $db->Query($st) === FALSE ) throw new Exception($db->Error());
  }
  
  /*
  * Delete variables
  */
  public function deleteVariables($vars){
    if ($vars == NULL) return;
    
    $db = new DBi();
    
    if (!isset($vars[0])) throw new Exception("Variables structure bad formed");

    $variables=implode(',', $vars);

    $dl = "DELETE FROM ".self::$TABLE." WHERE id IN ( $variables )";
    
    if ( $db->Query($dl) === FALSE ) throw new Exception($db->Error());
  }


}

?>

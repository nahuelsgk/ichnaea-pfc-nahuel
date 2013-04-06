<?php
namespace Domain;

use DBi;

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

  private $mid = '';
  private $name = '';
  private $public = false;
  private $sample = array();
  private $vars = array();
  private $matrix_building=array(array());


  public function __construct(){
  }

  public function __call($function , $args) {
    $calling = explode ('_' , $function);
    //printVar($calling);
    //printHTML("Calling generic method. The size of explode is". sizeof($calling));

    if(!empty($calling)){
      $name = $calling[0];
      $var  = $calling[1];
      //printHTML("Will resolve the generic call: name: $name; var: $var");
      //printVar($calling);
      if ($name == 'get' && isset($this->$var)) {
        return $this->$var;
      }
      if ($name == 'set' && isset($this->$var)) {
        $this->$var= $args[0];
        return;
      }
    trigger_error ("Fatal error from the generic: Call to undefined method Domain::Matrix::$function()");
    }
  }


  /*
  * Build basic info object
  *
  * Last update: 23 march 2013
  */
  public function initMatrix($mid){
    $db = new DBi();
    $matrix = $db->QuerySingleRowArray("SELECT * FROM matrix WHERE id='$mid'", MYSQL_ASSOC);
    $this->name = $matrix["name"];
    $this->mid = $matrix["id"];
    $this->public = $matrix["public"];
    $this->vars = \Domain\Vars::getVarsFromMatrixs($this->mid);
  }

  /*
  * Saves a new matrix. 
  */
  public function newMatrix($name, $user_id, $pid = NULL){
    $db = new DBi();  
    $string = $db->QuerySimple("INSERT INTO matrix(name, status, creator) VALUES ('%s', 'open', '%d')",array($name, $user_id),"execute");
    $mid = $db->GetLastInsertId();
    if(isset($pid)){
      //Insert into the project
      $string = $db->QuerySimple("INSERT INTO project_matrix(project_id, matrix_id) VALUES ('%d', '%d')",array($pid, $mid), "execute");
    }
    return $mid;
  }

  /*
  * Updates a matrix information.
  * - new_values: Assoc Array ["field"] => new_value
  * 
  */
  public function updateMatrix($mid, $new_values = NULL){
    if (!isset($new_values)) return;
    $db = new DBi();
    $sql = $db->BuildSQLUpdateGeneric("execute", self::$TABLE, self::$FIELDS, $new_values, array("id" => $mid));
  }
 
  /*
  * Returns an associative multidimesional array:
  * [
      "variables" => array( 
        [
          "vid" => "variable_id", "mid_vid" => "id of the usage of this var in the matrix", "v_name" => name_of_the_variable  
        ],...
      ),
      
    ]
    
  */
  public function buildMatrix($mid){
    /*TESTS*/
    $db = new \DBi();
    $query = "SELECT v.id as vid, v.name as vname, v.threshold as vthreshold FROM matrix_vars mv INNER JOIN var v ON mv.var_id = v.id WHERE matrix_id = %d";
    $samples = $db->QuerySimple($query, array($mid),'execute_return');
    $matrix = array();
    $headers = array();
    foreach($samples as $sample){
      $headers[$sample['vid']] = array("name" => $sample["vname"], "threshold" => $sample["vthreshold"]);
    }
    return $headers;
  }
 
  /*
  * Add a sample to a matrix. Returns the created id.
  */
  public function addEmptySample($mid){
    $db = new DBi();
    $db->QuerySimple("INSERT INTO sample(matrix_id) VALUES (%d)", array($mid), 'execute');
    return $db->GetLastInsertId();
  }
  /*
  * NAMESPACES functions
  */

  /*
  * Namespace function: Returns a list of matrixs
  *  - $fields(opt): select some columns, if NULL select all
  *  - $params:
  *    - "only_public": returns only public
  *  Last update: 12 march 2013
  */
  public static function listMatrixs($fields = NULL, $params = NULL){
    $db = new DBi();
 
    if (isset($params["only_public"]) && $params["only_public"]){
      $where["public"] = 'y';
     }
     $matrixs = $db->BuildSQLSelectGeneric("execute_local_catch", self::$TABLE, self::$FIELDS, empty($where) ? '' : $where );
     return $matrixs;
  }

  public static function listMatrixsVisibleByUser($user_id){
    $db = new Dbi();
    $sql = "SELECT * FROM matrix WHERE public = 'y' OR creator IN ('%d', 'NULL')";
    $matrixs = $db->QuerySimple($sql, array($user_id), "execute_return");
    return $matrixs;
  }
}
?>

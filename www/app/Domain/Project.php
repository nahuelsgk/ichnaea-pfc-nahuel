<?php 
namespace Domain;

includeLib("Domain/Matrix");

use DBi;
use Domain\Matrix;

class Project{

  #Fields Containing the columns for the database
  private static $FIELDS = array (
    'id'	=> DBi::SQLVALUE_NUMBER,
    'name' 	=> DBi::SQLVALUE_TEXT,
    'creator' 	=> DBi::SQLVALUE_NUMBER, 
    'active' 	=> DBi::SQLVALUE_Y_N,
    'created' 	=> DBi::SQLVALUE_DATETIME);
  private static $TABLE = 'projects';

  private $name = '';
  private $id = '';
  private $creator = '';

  public function __construct()
  {
  }
  
  /*
  * Generic method for getters and setters.
  * Notice: must be invoked get_ or set_ plus the name of the attribute
  */
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
      trigger_error ("Fatal error from the generic: Call to undefined method Domain::Project::$function()");
    }
  }

  /*
  * Builds the object. Will be growing
  *
  * Last update: 2 March 2013
  */
  public function initProject($id, $creator = NULL){

    if (isset($id))		$this->id = $id;
    if (isset($creator)) 	$this->creator = $creator;
    
    if (!isset($creator)){
       $project 	= $this->getProject($id, array("name","creator"));
       $this->name      = $project["name"];
       $this->id        = $id;
       $this->creator  	= $project["creator"];
    }
  }

  /*
  * Saves the project into the database
  * - $name: name of the project
  * - $creator: id of the user which creates the project
  * - $matrixs: array of id that are used in the matrix
  * Return the ID of the last saved project
  *
  * Last update: 3 march 2013
  */
  public function newProject($name, $creator){
    
    $db = new DBi();
    $insert = $db->BuildSQLInsert(
      self::$TABLE,
      array(
        'name' => DBi::SQLValue($name,self::$FIELDS["name"] ),
	'creator' => DBi::SQLValue($creator, self::$FIELDS["name"] ),
      )
    );
    $db->Query($insert);
    if($db->Error()) throw new \Exception("Domain:Project:newProject: Error en la insercion");
    $ret = $db->GetLastInsertId();
    return $ret;
  }

  /*
  * Get the project info with id $pid
  * - $fields(opt): array with the fields needs. If NULL, returns all
  * 
  * Last updated: 3 march 2013
  */
  public function getProject($pid, $fields = NULL){
    $db = new DBi();
    $st = $db->BuildSQLSelect(
      self::$TABLE,
      array("id" => DBi::SQLValue($pid, DBi::SQLVALUE_NUMBER)),
      isset($params) ? $params : array_keys(self::$FIELDS)
      );
    if (($project = $db->QuerySingleRowArray($st, MYSQL_ASSOC)) === FALSE) throw new Exception($db->Error());
    return $project;
  }
 
  /*
  * Get the matrixs from a project
  * - Type: type of selected matrixs
  *   - available: returns an array with the available matrixs for this project(if not project initiated, return all public available)
  *   - included: return an array with the included matrixs that are included in the project
  *   - both: returns both cases in a array of two arrays
  * Last update: 4 march 2013
  */
  public function getMatrixs($type = "both"){
    $ret;
    $db = new DBi();
    if ($type == "both" || $type =="available"){
      //Select public not included in my projects
      if (!empty($this->id)){
        // public not included in my projects
        $query = "SELECT * FROM matrix WHERE id NOT IN (SELECT matrix_id FROM project_matrix WHERE project_id = ".$this->id." )";
        if (($available_matrixs = $db->QueryArray($query, MYSQL_ASSOC)) === FALSE) throw new Exception($db->Error());
        $ret = $available_matrixs;
      }
      //Select public
      else{
        $available_matrixs = Matrix::listMatrixs(NULL, array("only_public" => 1));
	$ret = $available_matrixs;
      }
    }

    if ($type == "both" || $type == "included"){
      // NAHUEL: MUST OVERRIDE the DBi function equivalent with this accepting an array
      $query = "SELECT * FROM matrix WHERE id IN (SELECT matrix_id FROM project_matrix WHERE project_id = %u)";
      $query = vsprintf($query, array($this->id));
      if (($included_matrixs = $db->QueryArray($query, MYSQL_ASSOC)) === FALSE) throw new Exception($db->Error());
      $ret = $included_matrixs;
    }

    if ($type == "both"){
      $ret = array();
      array_push($ret, $available_matrixs);
      array_push($ret, $included_matrixs);
    }
    return $ret;
  }
  

  /*
  *  Adds a bunch of matrixs into a project.
  * - pid: project id
  * - set_of_mids: array with matrixs ids
  *
  * Last update: 3 march 2013
  */
  public function addSetMatrixs($pid, $set_of_mids = NULL){
    if ($set_of_mids == NULL) return;
    $db = new DBi();

    /*Code to delete
    $store = "INSERT INTO project_matrix (project_id, matrix_id) VALUES ";
    foreach($set_of_mids as $mid){
      $store_values [] = '('. DBi::SQLValue($pid, DBi::SQLVALUE_NUMBER) . ','. DBi::SQLValue($mid, DBi::SQLVALUE_NUMBER). ')';
    }
    $store .= implode(',', $store_values);
    printHTML($store);
    */

    foreach ($set_of_mids as $mid){$store_values[] = array($pid, $mid); }
    $store = $db->BuildSQLInsertMassive(
      "project_matrix",
      array( 
        "project_id" => DBi::SQLVALUE_NUMBER,
	"matrix_id"  => DBi::SQLVALUE_NUMBER,
      ),
      $store_values
    );
    //printHTML("<".$store2.">");
    if($db->Query($store) === FALSE) throw new Exception($db->getMessage());
  }

  /*
  *  Removes a bunch of matrixs from a project.
  * - pid: project id
  * - set_of_mids: array with matrixs ids
  *
  * Last update: 5 march 2013
  */
  public function remSetMatrixs($pid, $set_of_mids = NULL){
    if ($set_of_mids == NULL) return;
    $db = new DBi();
    
    $sql = $db->QuerySimple( "DELETE FROM project_matrix WHERE project_id = %u AND matrix_id IN %s",array($pid, $set_of_mids),"execute_local_catch");
  }

  /*
  * Updates a projects into the database
  * - values: an associative array which [column] = new Value
  *
  * Last update: 5 march 2013
  */
  public function updateProject($values){
    $db = new DBi();
    $sql = $db->BuildSQLUpdateGeneric('execute', self::$TABLE, self::$FIELDS, $values, array('id' => $this->id));
  }

  /*
  * Namespaces functions: functions related to the generic projects
  */

  /*
  * Return a list of projects
  * - $user_id: projects from a user
  * - $conditions(optional): associative array. 
  *   [only_active]: will select only enabled projects
  *  
  * Last update: 12 march 2013
  */
  public static function listProjects($user_id = NULL, $conditions = NULL){
    $db = new \DBi();

    $params = array("creator" => $user_id);

    if(isset($conditions["only_active"])){
      $params["active"] = 'y';
    }
    try{
      $result = $db->BuildSQLSelectGeneric("execute_local_catch", self::$TABLE, self::$FIELDS, $params );
      return $result;
    }
    catch(Exception $e){
      printHTML($e->getMessage());
    }
    return;
  }
}
?>

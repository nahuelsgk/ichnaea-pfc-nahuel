<?php 

includeLib("Domain/Matrix");

class Project{

  #Fields Containing the columns for the database
  private static $FIELDS = array ('name','user_owner', 'created');
  private static $TABLE = 'projects';
  private $name;
  private $user_owner;

  public function __construct(array $attributes)
  {
    $this->name = DBi::SQLValue($attributes['name']);
    $this->user_owner = DBi::SQLValue($attributes['user_owner'], DBi::SQLVALUE_NUMBER);
  }
  
  public function getProjectById(int $project_id)
  {
    
  }

  public function getProjectsByUser(int $user_id){
  
  }

  public function updateProject(int $id, $params){

  }
  
  /*
  * CRUDS FUNCTIONS
  */

  /*
  * Saves to the DB the object
  */
  public function saveProject(){
    $db = new DBi();
    $values=array();
    foreach (array('name','user_owner') as $v) { $values[$v] = $this->$v; }

    $st = $db->BuildSQLInsert(
      self::$TABLE, 
      $values
    );
    $db->Query($st);
  }

  /*
  * NAMESPACE FUNCTIONS
  */
  public function getMatrixs(int $project_id){
    return Matrix::getMatrixsFromProject($project_id);
  }

  /*
  * Returns the attributes. Returns an associative array
  */
  public function getProjectAttributes($pid, $atts){
    $db = new DBi();
    $st = $db->BuildSQLSelect(
      self::$TABLE,
      array("id" => DBi::SQLValue($pid,DBi::SQLVALUE_NUMBER)),
      $atts ? $atts : self::FIELDS
    );
    return $db->QueryArray($st);
  }
}
?>

<?php 

class Project{

  #Fields Containing the columns for the database
  private static $FIELDS = array ('name','user_owner', 'created');
  private static $TABLE = 'projects';
  private $name;
  private $user_owner;

  public function __construct(array $attributes)
  {
    printHTML("Creando un proyecto");
    printVar($attributes);
    $this->name = MySQL::SQLValue($attributes['name']);
    $this->user_owner = MySQL::SQLValue($attributes['user_owner'], MySQL::SQLVALUE_NUMBER);
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
    $db = new MySQL();
    $values=array();
    foreach (array('name','user_owner') as $v) { $values[$v] = $this->$v; }

    $st = $db->BuildSQLInsert(
      self::$TABLE, 
      $values
    );
    $db->Query($st);
  }

}
?>

<?php 

includeLib("Domain/Matrix");

class Project{

  #Fields Containing the columns for the database
  private static $FIELDS = array (
    'name' => DBi::SQLVALUE_TEXT,
    'user_owner' => DBi::SQLVALUE_NUMBER, 
    'created' => DBi::SQLVALUE_DATETIME);
  private static $TABLE = 'projects';
  private $name;
  private $user_owner;

  public function __construct(array $attributes)
  {
    $this->name = DBi::SQLValue($attributes['name']);
    $this->user_owner = DBi::SQLValue($attributes['user_owner'], DBi::SQLVALUE_NUMBER);
  }
  /*
  * NAMESPACE FUNCTIONS
  */

  /*
  * Saves to the DB the object
  */
   public function saveProject2($user_id, $name){
    $db = new DBi();

    $st = $db->BuildSQLInsert(
      self::$TABLE,
      array(
       'name' => DBi::SQLValue($name, self::$FIELDS["name"]),
       'user_owner' => DBi::SQLValue($user_id, self::$FIELDS["user_owner"]),
      )
    );
    if ($db->Query($st) === FALSE ) throw new Exception($e->getMessage());
    return $db->GetLastInsertId();
  }
  
  /*
  * May be out
  */
  public function getMatrixs(int $project_id){
    return Matrix::getMatrixsFromProject($project_id);
  }

  /*
  * Returns the attributes. Returns an associative array
  * - params(optional): array of a concrete fields
  */
  public function getProjectAttributes($pid, $params = NULL){
    $db = new DBi();
    $st = $db->BuildSQLSelect(
      self::$TABLE,
      array("id" => DBi::SQLValue($pid,DBi::SQLVALUE_NUMBER)),
      isset($params) ? $params : array_keys(self::$FIELDS)
    );
    if (($project = $db->QuerySingleRowArray($st, MYSQL_ASSOC)) === FALSE) throw new Exception($db->Error());
    return sizeof($project) == 1 ? current($project) : $project;
  }
}
?>

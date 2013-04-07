<?php
namespace Domain\Vars;

use DBi;

class SingleVar{
 
  private static $TABLE = 'single_var';
  private static $FIELDS = array(
    'name' => DBi::SQLVALUE_TEXT
  );

  private $id = null;

  public function __construct($id = NULL){
    if(isset($id)) $this->id = $id;
  }

  /*
  * Return a list of single vars
  */
  public function getList(){
    $db = new \DBi();
    $list_single_var = $db->BuildSQLSelectGeneric('execute', self::$TABLE, self::$FIELDS, null, array("svid"=>"id", "name"));
    return $list_single_var;
  }

  /*
  * Returns a complex structure with single vars and seasons 
  * ["svid"] => [
      "name"    => ...
      "seasons" => [
        "ssid"  => ...
	"name"  => ...
	"notes" => ...
      ]
    ]
  */
  public function getCompleteList(){

    $db = new \DBi();
    $vars = $db->QuerySimple(
      "SELECT sv.id as svid, sv.name as sv_name, sv.notes as sv_notes, s.id as s_id, s.name as s_name, s.notes as s_notes 
      FROM single_var sv LEFT JOIN season s ON sv.id = s.single_var_id", null, "execute_return");
    $completeData = array();
    foreach ($vars as $var){
      $completeData[$var['svid']]['name'] = $var['sv_name'];
      if (isset($var['s_id'])) {
         if(!isset($completeData[$var['svid']]['seasons'])) $completeData[$var['svid']]['seasons'] = array();
         array_push($completeData[$var['svid']]['seasons'], array('ssid' => $var['s_id'], 'name' => $var['s_name'], 'notes' => $var['s_notes']));
      }
    }
    return $completeData;
  }
  /*
  * Return recent id created
  */
  public function save($name){
    $db = new \DBi();
    $sql = $db->BuildSQLInsert(self::$TABLE, array('name' => DBi::SQLValue($name,DBi::SQLVALUE_TEXT)));
    $db->Query($sql);
    $this->id=$db->GetLastInsertId();
    return $this->id;
  }

  public function addSeason($name, $notes = NULL){
    $season = new Season();
    $season->save($this->id, $name, $notes);
  }
  
  public function getSeasonsList(){
  	return Season::getSeasonsList($this->id);
  }
}

class Season{
  private static $TABLE = 'season';
  private static $FIELDS = array(
    'single_var_id'   => DBi::SQLVALUE_NUMBER,
    'name'            => DBi::SQLVALUE_TEXT,
    'notes'           => DBi::SQLVALUE_TEXT
  );

  private $id;

  public function __contruct(){}

  public function save($vid, $name, $notes){
    $db = new \DBi();
    $sql = $db->BuildSQLInsert(self::$TABLE, array('name' => DBi::SQLValue($name,DBi::SQLVALUE_TEXT),'notes' => DBi::SQLValue($notes,DBi::SQLVALUE_TEXT), 'single_var_id' => DBi::SQLValue($vid, DBi::SQLVALUE_NUMBER)));
    $db->Query($sql);
    $this->id = $db->GetLastInsertId();
    return $this->id;
  }
  
  public static function getSeasonsList($svid){
  	$db = new \DBi(); 	
    $list_single_var = $db->BuildSQLSelectGeneric('execute_local_catch', self::$TABLE, self::$FIELDS, array("single_var_id"=>$svid), null);
    printVar($list_single_var);
    return $list_single_var;
  }
}
?>

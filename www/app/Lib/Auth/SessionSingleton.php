<?php
class SessionSingleton{

  private $sid;
  private static $instance;
  public function __construct(){
    session_start();
  }
 
  public static function getInstance()
  {
    if (!self::$instance instanceof self)
    {
      self::$instance = new self;
    }
    return self::$instance;
  }
  
  /*
  * Create the session
  */
  public function createSession($email){
    $_SESSION['sid'] = $email;
  }
 
  /*
  * Check if the exists exists
  */
  public function checkSession()
  {
    if(!$this->existsSession($_SESSION['sid'])) redirectLoginRegistration();
  }
  
  
  /*
  * Check if the session exists
  */
  private function existsSession()
  {
    if(isset($_SESSION['sid'])) return true;
    else false;
  }

  /*
  * Delete the current session
  */
  public function deleteSession(){
    unset($_SESSION['sid']);
  }

  /*
  * Delete the current session
  */
  public function getUserId(){
    $db = new DBi();
    $sql = "SELECT id  FROM users WHERE login='".$_SESSION['sid']."'";
    $user_id = $db->QuerySingleRowArray($sql);
    return $user_id[0];     
  }

  public function printSessions(){
    var_dump($_SESSION);
  }
}
?>

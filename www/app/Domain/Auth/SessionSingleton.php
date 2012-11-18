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
  # Checking session
  public function createSession($email){
    $_SESSION['sid'] = $email;
  }

  public function checkSession()
  {
    if(!$this->existsSession($_SESSION['sid'])) redirectLoginRegistration();
  }
  
  private function existsSession()
  {
    if(isset($_SESSION['sid'])) return true;
    else false;
  }
  public function deleteSession(){
    unset($_SESSION['sid']);
  }

  public function getUserId(){
    $db = new MySQL();
    $sql = "SELECT id  FROM users WHERE login='".$_SESSION['sid']."'";
    $user_id = $db->QuerySingleRowArray($sql);
    return $user_id[0];     
  }

  public function printSessions(){
    var_dump($_SESSION);
  }
}
?>

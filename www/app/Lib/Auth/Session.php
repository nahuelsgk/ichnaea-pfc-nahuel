<?php

session_start();
class Session{

  private $sid;

  public function __construct(){
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

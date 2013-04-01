<?php

namespace Lib\Auth; 


class Session{

  private $sid;
 
  public function __construct(){
    if(!isset($_SESSION)) {
      session_start();
    }
  }
  
  /*
  * Creates a sessions and prepares the cookie
  */
  public function createSession($email){
    $value = $this->generateCookieValue($email);
    $_SESSION[$value] = $email;
    setcookie("auth_key", $value, time()+60*60, '/','ichnaea.lsi.upc.edu', false, false); 
  }

  private function generateCookieValue($value){
    return md5($value."fluzzy");
  }
  
  /*
  * Reads the cookie
  */
  public function checkSession(){
    if(!$this->isValid()) redirectLoginRegistration();
  }
 
  /*
  * Is a valid session
  */
  public function isValid(){
    if(isset($_COOKIE['auth_key'])){
      $cookie_value = $_COOKIE["auth_key"];
      if(!$this->existsSession($cookie_value)) return false;
    }
    else{
      printHTML("Problemas leyendo la cookie");
      return false;
    }
    return true;
  }
  
  /*
  *
  */
  private function existsSession($cookie_value){    
    if(isset($_SESSION[$cookie_value])) return true;
    else false;
  }

  public function deleteSession(){
    $cookie_value = $_COOKIE["auth_key"];
    unset($_SESSION[$cookie_value]);
  }

  public function getUserId(){
    $cookie_value = $_COOKIE["auth_key"];
    $db = new \DBi();
    $sql = "SELECT id  FROM users WHERE login='".$_SESSION[$cookie_value]."'";
    $user_id = $db->QuerySingleRowArray($sql);
    return $user_id[0];     
  }
}
?>

<?php

class User
{
  public $user_id;

  public function __construct($user_id){
    $this->user_id = $user_id;
  }   

  //Getters
  public function checkLogin($email, $password)
  {
    $db = new MySQL(); 
    $sql = "SELECT * FROM users WHERE login='".$email."' AND passwd='".$password."'";
    $db->Query($sql); 
    if ($db->RowCount() > 0) return true;
    return false;
  }

  /**
  * Get the projects associated who the user is the owner. 
  * Params:
  *  - $user_id
  * Returns:
  *  - Array asociated 
  */
  public function getProjects()
  {
    $db = new MySQL();
    $sql = "SELECT name,status FROM projects WHERE user_owner='$this->user_id'";
    $ret = $db->QueryArray($sql,MYSQL_BOTH);
    return $ret;
  }

  //CRUDS
  public function createUser(){}
  public function removeUser(){}
  public function disableUser(){}
  public function enableUser(){}
  public function updateUser(){}
}
?>

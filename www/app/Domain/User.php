<?php

class User
{
  private $user_id;

  public function __construct($user_id = NULL){
    $this->user_id = $user_id;
  }   

  //Getters
  /*
  * Get the id in the database
  */
  public function getId()
  {
    return $this->user_id;
  }

  /*
  * Get the name
  */
  public function getName()
  {
    $db = new MySQL();
    $sql = $db->BuildSQLSelect("users",array("id"=>$this->user_id),"name");
    $result = $db->QuerySingleValue($sql);
    return $result;
  }
  /*
  * Returns true if the user is administrator
  */
  public function isAdministrator(){
    $db = new MySQL();
    $sql = $db->BuildSQLSelect("users",array("id"=>$this->user_id),"administrator");
    $result = $db->QuerySingleValue($sql);
    return $result == 'y' ? true : false;
  }

  /*
  * Check the credentials of a user
  * Returns:
  *  - TRUE if exists
  *  - FALSE if not exists
  */
  public function checkCredentials($email, $password, $assign = false)
  {
    $db = new MySQL();
    $sql = "SELECT id FROM users WHERE login='".$email."' AND passwd='".$this->encryptUserPassword($password)."'";
    $user = $db->QuerySingleRowArray($sql);
    if ( !empty($user) ) { 
      $this->user_id = $user["id"];
      return TRUE;
    }
    return FALSE;
  }
  
  /*
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

  /*
  * Creates a user in the database. The $user_info is an array associated.
  * Returns:
  * - undef: An error has happened
  * - 0: the user already exists
  * - 1: user created
  */
  public function createUser($user_info){
    $db = new MySQL();

   # check if the user exists
   $sql = "SELECT 1 FROM users WHERE login='".$user_info['login']."'";
   $user = $db->QuerySingleRowArray($sql);
   if ( !empty($user) ) {
     return 0;
   }

   # If the users don't exists, encrypt the password
   $user_info['passwd'] =  $this->encryptUserPassword($user_info['passwd']);
   
   # Prepare the values as SQL expects
   foreach ($user_info as &$info){
     $info = MySQL::SQLValue($info);
   }

   # execute the sql
   $result = $db->InsertRow('users',$user_info);

   if (! $result) 
   {
     $db->kill();
     return undef;
   }
   return 1;
  }

  public function removeUser(){}

  public function disableUser(){}

  public function enableUser(){}

  public function updateUser(){}


  //Privates
  private function encryptUserPassword($string){
    return md5($string."ipfc"); 
  }
}
?>

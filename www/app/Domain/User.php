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
    $db = new DBi();
    $sql = $db->BuildSQLSelect("users",array("id"=>$this->user_id),"name");
    $result = $db->QuerySingleValue($sql);
    return $result;
  }

  /*
  * Chech if the user has the privileges passed by params.
  * By now, the only privileges are 'administrator'
  */
  public function hasPrivileges($privileges){
    return $this->isAdministrator();
  }
  /*
  * Returns true if the user is administrator
  */
  public function isAdministrator(){
    $db = new DBi();

    $sql = $db->BuildSQLSelect(
      "users",
      array(
        "id"=>$this->user_id
      ),
      "administrator");

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
    $db = new DBi();
    $sql = "SELECT id FROM users WHERE login='".$email."' AND passwd='".$this->encryptUserPassword($password)."'";
    $user = $db->QuerySingleRowArray($sql);
    printVar($db->Error());
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
    $db = new DBi();
    $sql = "SELECT id, name FROM projects WHERE user_owner='$this->user_id'";
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
    $db = new DBi();

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
     $info = DBi::SQLValue($info);
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

  /*
  * Reset the password to "fluzzy_909"
  * Return FALSE if happened any error
  */
  public function resetPassword($user_id){
    $db = new DBi();
    $result = $db->UpdateRows("users",
    		array("passwd"=>DBi::SQLValue(User::encryptUserPassword("fluzzy_909"))), 
		array("id"=>$user_id));
    if ($result === FALSE) $db->kill();
    return $result;
  }

  public function changePassword($new_passwd){
    $db = new DBi();
    $result = $db->UpdateRows("users",
    		array("passwd"=>DBi::SQLValue(User::encryptUserPassword($new_passwd))), 
		array("id"=>$this->user_id));
    if ($result === FALSE) $db->kill();
    return $result;
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

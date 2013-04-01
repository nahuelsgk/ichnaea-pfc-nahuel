<?
includeLib('Domain/User');

use \Domain;
/*
* Controller for the page admin/users
*/
function displayListUsersPage($page){
  $error='';
  #Get the parameters
  if (isset($_POST["submit"])) {
    if(isset($_POST["reset_paswd"])){
      foreach($_POST["reset_paswd"] as $user_id){
        $ret = \Domain\User::resetPassword($user_id);
        if ($ret === FALSE) $error = "An error has happened";
      }
    }
  }
  $users = getUsersList(); 
  $page->assign("error",$error);
  $page->assign("users",$users);
}

/*
*  Return an associative array with the list of the users
*/
function getUsersList(){
  $db = new DBi();
  $sql = "SELECT id,name,login FROM users";
  $ret = $db->QueryArray($sql,MYSQL_BOTH);
  return $ret;
}
?>

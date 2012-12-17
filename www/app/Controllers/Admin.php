<?
includeLib("Lib/Admin");

/*
* Controller for the page admin/users
*/
function displayListUsersPage($page){
  
  #Get the parameters
  if ($_POST["submit"]){
    printHTML("Submitted!"); 
  }
  $users = getUsersList(); 
  $page->assign("users",$users);
}
?>

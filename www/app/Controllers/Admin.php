<?
includeLib("Lib/Admin");

/*
* Controller for the page admin/users
*/
function displayListUsersPage($page){
  $users = getUsersList(); 
  $page->assign("users",$users);
}
?>

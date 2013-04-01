<?php
includeLib("Domain/User");
includeLib("Lib/Auth/Session");

/*
Check if the current user logged has the privileges

Params: 
- privileges: which privilege needs this page 

*/
function requirePrivileges($page, $privileges){
  $session = new \Lib\Auth\Session();
  $user_id = $session->getUserId();
  $user = new Domain\User($user_id);
  if ($user->hasPrivileges($privileges)) return '';
  else redirectHome(); 
}

?>

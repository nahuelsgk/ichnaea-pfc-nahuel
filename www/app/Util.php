<?php
includeLib("Lib/Auth/SessionSingleton");
includeLib("Domain/User");


/*
Check if the current user logged has the privileges

Params: 
- privileges: which privilege needs this page 

*/
function requirePrivileges($page, $privileges){
  $user_id = SessionSingleton::getInstance()->getUserId();
  $user = new User($user_id);
  if ($user->hasPrivileges($privileges)) return '';
  else redirectHome(); 
}

?>

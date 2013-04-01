<?php
includeLib("Domain/User");

use \Lib\Auth;
use \Domain;
/*
* Page controller for the user settings
*/
function displaySettings($page, $params){
 if($params->getParam('save')){
   $session = new \Lib\Auth\Session();
   $user_id = $session->getUserId();
   $user = new \Domain\User($user_id);
   if (isset($_POST["passwordsignup"]))
   {
     $user->changePassword($_POST["passwordsignup"]);
   }
 }
}
?>

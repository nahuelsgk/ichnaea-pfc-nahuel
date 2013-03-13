<?php
includeLib("Domain/User");


/*
* Page controller for the user settings
*/
function displaySettings($page, $params){
 if($params->getParam('save')){
   $session = new Session();
   $user_id = $session->getUserId();
   $user = new User($user_id);
   if (isset($_POST["passwordsignup"]))
   {
     $user->changePassword($_POST["passwordsignup"]);
     printHTML("Password Changed successfully");
   }
 }
}
?>

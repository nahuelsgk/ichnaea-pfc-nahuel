<?php
includeLib("Domain/User");
includeLib("Domain/Auth/SessionSingleton");


/*
* Page controller for the user settings
*/
function displaySettings($page){
 if(isset($_POST['save'])){
   $user = new User(SessionSingleton::getInstance()->getUserId());
   printVar($user);
   if (isset($_POST["passwordsignup"]))
   {
     $user->changePassword($_POST["passwordsignup"]);
     printHTML("Password Changed successfully");
   }
 }
}
?>

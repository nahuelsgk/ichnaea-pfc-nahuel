<?php
includeLib("Domain/User");
includeLib("Lib/Auth/SessionSingleton");


/*
* Page controller for the user settings
*/
function displaySettings($page){
 if(isset($_POST['save'])){
   $user = new User(SessionSingleton::getInstance()->getUserId());
   if (isset($_POST["passwordsignup"]))
   {
     $user->changePassword($_POST["passwordsignup"]);
     printHTML("Password Changed successfully");
   }
 }
}
?>

<?php
#Try to abstract the include in a format
includeLib("Lib/Auth/Login");

use Lib\Auth;
use \Domain;

function displayLoginRegistrationForm($page, $params){
  $session = new Lib\Auth\Session();
  $message="";
  $error = "";
  $messages = array (
    "error_user_exist" => "Sorry, but this account already exits in the system",
    "error_database"   => "Sorry, but we have a database problems. Please report to nahuelsgk@gmail.com", 
    "error_password_length" => "Sorry, but the password must be between minimum 4 letters",
    "error_incorrect_login" => "Sorry, wrong login. If you forgot your password, please use the reminder link below the form",
    "success_user_created" => "Your use has been created. Now you can login.(Still no has a email server to confirm the email)."
  );

  # Submit on the login form
  if($params->getParam('login')){
    if(Lib\Auth\Login::checkLogin($params->getParam("username"),$params->getParam("password"))==true){
      $session->createSession($_POST["username"]);
      redirectHome();
    }
    else{
	  $error = "login";
	  $message="error_incorrect_login";
    }
  }

  else if($params->getParam('register')){
    includeLib("Domain/User");
    $user_info['name'] = $_POST['username'];
    $user_info['login'] = $_POST['email'];
    $user_info['passwd'] = $_POST['passwordsignup'];
    $user = new \Domain\User();
    $result = $user->createUser($user_info);
    switch($result){
      case 1:
	redirectTo('success'); 
	break;
      case 0:
	$message="error_user_exist";
	$error="register";
	break;
    }
    
  } 

  $page->assign('error', $error);
  $page->assign('message',$message!="" ? $messages[$message] : "");
}
?>

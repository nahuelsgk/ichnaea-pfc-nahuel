<?php
#Try to abstract the include in a format
includeLib("Domain/Auth/Login");
includeLib("Domain/Auth/SessionSingleton");

#Abstract the Smarties Class.
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->caching = false;
$login = new Login();
$session = SessionSingleton::getInstance();
$error=0;

# Submit on the form
if($_POST){
  if($login->checkLogin($_POST["email"],$_POST["passwd"])==true){
    $session->createSession($_POST["email"]);
    redirectHome();
  }
  else{
  	$error=1;
  }
}

$smarty->assign('error', $error);
?>

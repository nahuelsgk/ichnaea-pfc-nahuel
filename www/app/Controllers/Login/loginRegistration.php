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
if(!empty($_POST['login'])){
  if($login->checkLogin($_POST["username"],$_POST["password"])==true){
    $session->createSession($_POST["username"]);
    redirectHome();
  }
  else{
  	$error=1;
  }
}

$smarty->assign('error', $error);
?>

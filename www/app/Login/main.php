<?php
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->caching = false;

function checkLogin($email, $password){
  if ($email == "nahuelsgk@gmail.com" && $password="12344567"){
    printHTMl("Autenticado\n");
    printHTML("Necesitamos crear una cookie");
    printHTML("Necesitamos una redirección al dashboard del usuario");
  }
  else {printHTML("Empezar a gestionar errores en pantalla");}
}

if($_POST){
  checkLogin($_POST["email"],$_POST["passwd"]);
}
else{
$smarty->display('./Login/login.tpl');
}
?>

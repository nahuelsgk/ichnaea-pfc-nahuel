<?php
#Smarty enviroment defien
require("./conf/Const.php");
echo SMARTY_DIR;
require(SMARTY_DIR.'Smarty.class.php');
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->caching = false;

$smarty->display('login.tpl');
?>

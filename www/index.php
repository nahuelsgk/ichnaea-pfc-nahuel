<?php
require("./app/Lib/Util.php");
require("./conf/Const.php");
require("./app/Lib/Handler.php");
require("./lib/mysql.class.php");
require(SMARTY_DIR.'Smarty.class.php');

#This a global variable: be careful to rename inside the code
$handler = new Handler($_SERVER['REQUEST_URI']);
$handler->executeController();
?>

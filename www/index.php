<?php
require("./app/Lib/Util.php");
require("./conf/Const.php");
require("./app/Lib/Handler.php");
require("./lib/UltimateMysql/mysql.class.php");
require('./lib/SmartyTemplating/Smarty.class.php');
require('./app/Lib/Params.php');
#This a global variable: be careful to rename inside the code
$handler = new Handler();
$params = new Params();
$handler->executeController();
?>

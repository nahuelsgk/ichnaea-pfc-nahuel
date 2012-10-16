<?php
#Smarty enviroment defien
require("./lib/Util.php");
require("./conf/Const.php");
require("./lib/Handler.php");
require(SMARTY_DIR.'Smarty.class.php');

$template = handler();
include($template);
?>

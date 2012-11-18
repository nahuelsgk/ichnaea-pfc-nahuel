<?php 
function smarty_function_call_controller($params, &$smarty){
  include(CONTROLLERS_PATH.$params["path"].".php");
}
?>

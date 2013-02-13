<?php 
function smarty_function_init($params, &$smarty){
  $path_file = ICHNAEA_CORE.$params["path"].".php";
  
  include_once($path_file);

  if(isset($params["function"])){
    global $globalparams;
    if(isset($params["params"])){
      call_user_func_array($params["function"],array($smarty, $globalparams, $params["params"]));
    }
    else{
      call_user_func_array($params["function"],array($smarty, $globalparams));
    }
  }

}
?>

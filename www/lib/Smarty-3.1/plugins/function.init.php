<?php 
function smarty_function_init($params, &$smarty){
  $path_file = ICHNAEA_CORE.$params["path"].".php";
  
  include($path_file);

  if(isset($params["function"])){ 
    if(isset($params["params"])){
      call_user_func_array($params["function"],array($smarty,$params["params"]));
    }
    else{
      call_user_func($params["function"],$smarty);
    }
  }

}
?>

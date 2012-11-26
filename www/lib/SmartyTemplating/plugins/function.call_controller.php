<?php 
function smarty_function_call_controller($params, &$smarty){
  $path_file = CONTROLLERS_PATH.$params["path"].".php";
  
  include($path_file);

  if(isset($params["function"])){ 
    call_user_func($params["function"],$smarty);
  }

}
?>

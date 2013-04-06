<?php
use Domain\Vars;

function display_var_list($page, $params){
  $vars = Domain\Vars\SingleVar::getCompleteList();
  $page->assign("single_vars", $vars);
}
?>

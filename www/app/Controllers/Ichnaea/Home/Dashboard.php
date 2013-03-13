<?php

use Domain\User;

/*
* Controller for /home
* 
* Last update: 4 march 2013
*/
function display_home($page){

  $user = new User(Util::getUserId());
  $projects = $user->getProjects();

  if(!$projects)
  {
    $page->assign('empty', 1);
    return;
  }
  else
  {
    $page->assign("projects",$projects);
    $page->assign('empty', 0);
  }
}
?>

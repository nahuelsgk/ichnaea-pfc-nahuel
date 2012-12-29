<?php
includeLib("Domain/User");
includeLib("Lib/Auth/SessionSingleton");
 
function display_home($page){
  $user_id = SessionSingleton::getInstance()->getUserId();

  $user = new User($user_id);
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

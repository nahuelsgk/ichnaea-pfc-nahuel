<?php
includeLib("Domain/User");
includeLib("Domain/Auth/SessionSingleton");
 
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
    $page->assign("matrixs",$projects);
    $page->assign('empty', 0);
  }
}
?>

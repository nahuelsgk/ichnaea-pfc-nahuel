<?php
includeLib("Domain/User");

$user_id = SessionSingleton::getInstance()->getUserId();

printHTML("El id del usuario es ".$user_id);
//SESSION MUST GIVE US THE USER
$user = new User($user_id);
$projects = $user->getProjects();

if(!$projects)
{
  $smarty->assign('empty', 1);
  return;
}
else
{
  $smarty->assign("matrixs",$projects);
  $smarty->assign('empty', 0);
}

?>

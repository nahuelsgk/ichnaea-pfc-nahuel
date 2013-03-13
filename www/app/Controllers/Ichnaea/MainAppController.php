<?php
includeLib("Domain/User");
includeLib("Lib/Auth/Session");

$session = new Auth\Session();
$session->checkSession();
$user_id = $session->getUserId();

$user = new Domain\User($user_id);
$is_admin = $user->isAdministrator();
$name = $user->getName();

$smarty->assign("is_admin",$is_admin);
$smarty->assign("username",$name);
?>

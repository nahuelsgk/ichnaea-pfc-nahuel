<?php
includeLib("Domain/Auth/SessionSingleton");
includeLib("Domain/User");

SessionSingleton::getInstance()->checkSession();
$user_id = SessionSingleton::getInstance()->getUserId();

$user = new User($user_id);
$is_admin = $user->isAdministrator();
$name = $user->getName();

$smarty->assign("is_admin",$is_admin);
$smarty->assign("name",$name);
?>

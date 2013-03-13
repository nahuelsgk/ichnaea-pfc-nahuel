<?php
includeLib("Lib/Auth/Login");
includeLib("Lib/Auth/Session");

$session = new Auth\Session();
$session->deleteSession();
redirectLoginRegistration();

?>

<?php
includeLib("Lib/Auth/Login");

use Lib\Auth;

$session = new Auth\Session();
$session->deleteSession();
redirectLoginRegistration();

?>

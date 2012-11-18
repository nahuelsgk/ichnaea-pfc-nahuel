<?php
includeLib("Domain/Auth/SessionSingleton");
includeLib("Domain/User");

// Check if it is a valid session
SessionSingleton::getInstance()->checkSession();

//Must check permissions

?>

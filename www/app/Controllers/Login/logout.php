<?php
includeLib("Domain/Auth/Login");
includeLib("Domain/Auth/SessionSingleton");

SessionSingleton::getInstance()->deleteSession();
redirectLoginRegistration();

?>

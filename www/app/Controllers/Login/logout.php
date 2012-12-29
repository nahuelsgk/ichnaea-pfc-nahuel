<?php
includeLib("Lib/Auth/Login");
includeLib("Lib/Auth/SessionSingleton");

SessionSingleton::getInstance()->deleteSession();
redirectLoginRegistration();

?>

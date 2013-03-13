<?php
namespace Auth;

includeLib("Domain/User");

class Login{
  /*
  * Checks if the credentials are correct
  * Params:
  *  - email: system's login
  *  - password: password without encrypting
  * Returns: 
  *  - TRUE if exits
  *  - FALSE if not exists
  */
  function checkLogin($email, $password){
    $user = new \Domain\User();
    return $user->checkCredentials($email,$password);
  }
}
?>

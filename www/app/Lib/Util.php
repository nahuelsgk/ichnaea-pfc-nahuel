<?php

/*
* PRINTERS
*/

function printHTML($string){
  error_log($string);
}

function printVar($var){
ob_start();
var_dump($var);
$contents = ob_get_contents();
ob_end_clean();
error_log($contents);
}

/*
* REDIRECTIONS
*/

function redirectLoginRegistration(){
  redirectTo('index');
}

function redirectHome(){
  redirectTo('home');
}

function redirect404(){
  redirectTo('notfound');
}

function redirectTo($path){
  header('Location: http://' . ROOT_DOMAIN_ICHNAEA . $path);
}

function reloadSafe(){
  global $handler;
  header('Location: '.$handler->getFullPath());
}
/*
* CALLS
*/
function includeLib($library){
  include_once(WORKPATH."app/$library.php");
}

class Util{
  public function getUserId(){
    includeLib('Lib/Auth/Session');
    $session = new Lib\Auth\Session();
    return $session->getUserId();
  }
  
  function redirectTo($path){
    header('Location: http://' . ROOT_DOMAIN_ICHNAEA . $path);
  }
}
?>

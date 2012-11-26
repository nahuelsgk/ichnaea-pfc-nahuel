<?php

/*
* PRINTERS
*/
function printHTML($string){
  echo "$string <br>";
}
function printVar($var){
  echo "<pre>";
  echo var_dump($var);
  echo "</pre>";

}

/*
* REDIRECTIONS
*/
function redirectLoginRegistration(){
  header('Location: http://'.ROOT_DOMAIN_ICHNAEA.'index');
}

function redirectHome(){
  header('Location: http://'.ROOT_DOMAIN_ICHNAEA.'home');
}

function redirect404(){
  header('Location: http://'.ROOT_DOMAIN_ICHNAEA.'notfound');
}

/*
* CALLS
*/
function includeLib($library){
  include_once(WORKPATH."app/$library.php");
}

?>

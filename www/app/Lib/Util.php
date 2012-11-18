<?php

function printHTML($string){
  echo "$string <br>";
}
function printVar($var){
  echo "<pre>";
  echo var_dump($var);
  echo "</pre>";

}
function redirectLoginRegistration(){
  header('Location: http://'.ROOT_DOMAIN_ICHNAEA.'index');
}

function redirectHome(){
  header('Location: http://'.ROOT_DOMAIN_ICHNAEA.'home');
}

function includeLib($library){
  include_once(WORKPATH."app/$library.php");
}

?>

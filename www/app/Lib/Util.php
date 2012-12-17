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

/*
* CALLS
*/
function includeLib($library){
  include_once(WORKPATH."app/$library.php");
}

?>
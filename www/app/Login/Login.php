<?php

function login($name,$password){
  if (strcmp($name,"nahuel") == 0 && strcmp($password,"qwerasdf")) return true;
  else return false;
}

?>

<?php 

/*
function load($namespace) {
  printHTML("Inside load. Trying to load this namespace:". $namespace);
  $splitpath = explode('\\', $namespace);
  printVar($splitpath);
  $path = '';
  $name = '';
  $firstword = true;
  for ($i = 0; $i < count($splitpath); $i++) {
    if ($splitpath[$i] && !$firstword) {
      if ($i == count($splitpath) - 1)
        $name = $splitpath[$i];
      else
        $path .= DIRECTORY_SEPARATOR . $splitpath[$i];
    }
    
    if ($splitpath[$i] && $firstword) {
      if ($splitpath[$i] != __NAMESPACE__){
        //First word and
        break;
      }
        $firstword = false;
      }
  }
  if (!$firstword) {
    $fullpath = __DIR__ . $path . DIRECTORY_SEPARATOR . $name . '.php';
    return include_once($fullpath);
  }  
  return false;
}

*/

function load($namespace){
  #printHTML("---- Trying to use a non-included previous class by method use: $namespace");
  $splitpath = explode('\\', $namespace);   
  $resolved_path = '';
  for ($i = 0; $i < count($splitpath); $i++) {
    $resolved_path .= DIRECTORY_SEPARATOR . $splitpath[$i];
  }

  $path_name = WORKPATH . 'app' . $resolved_path . '.php'; 
  #printHTML("---- The resolved one is: $path_name");
  include_once($path_name);
}

function loadPath($absPath) {
  return include_once($absPath);
}

spl_autoload_register(__NAMESPACE__ . '\load', true);

?>

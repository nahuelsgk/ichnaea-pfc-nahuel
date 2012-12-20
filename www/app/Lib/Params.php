<?php

/*
* This class will handle all the params cleaned from injections
*/

class Params{

  private $paramsGET;
  private $paramsPOST;
 
  //Read from querid params and construc both associative arrays
  public function __construct(){
    $this->constructPostParams();
    $this->constructGetParams();
  }

  private function constructPostParams(){
    foreach($_POST as $k => $v){
    }
  }

  private function constructGetParams(){
  
  }

  /*
  * Check if possible parameter's hacking like SQL INJECTION's
  */
  private function checkCleanParameter(){
  
  }
}

?>

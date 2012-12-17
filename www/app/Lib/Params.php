<?php

/*
* This class will handle all the params cleaned from injections
*/

class Params{

  private $paramsGET;
  private $paramsPOST;
 
  //Read from querid params and construc both associative arrays
  public function __construct(){
    printVar($_POST);
    printVar($_GET);
  }

}

?>

<?php

/*
* This class will handle all the params cleaned from injections
*/

class Params{

  private $paramsGET;
  private $paramsPOST;
  
  /*
  * Read from queried params and construc both associative arrays
  */
  public function __construct(){
    $this->constructPostParams();
    $this->constructGetParams();
  }

  /*
  * Get the params
  */
  public function getParam($paramName){
    if(isset($this->paramsGET[$paramName])){
      return $this->paramsGET[$paramName];
    }
    elseif(isset($this->paramsPOST[$paramName])){
      return $this->paramsPOST[$paramName];
    }
    return;
  }
  
  /*
  * Check if a POST param was defined and return it.
  * Otherwise returns.
  */
  public function getPOSTParam($paramName){
    if(isset($this->paramsGET[$paramName])){
      return $this->paramsGET[$paramName];
    }
    return;
  }

  /*
  * Check if a GET param was defined and return it.
  * Otherwise returns.
  */
  public function getGETParam($paramName){
    if(isset($this->paramsGET[$paramName])){
      return $this->paramsGET[$paramName];
    }
    return;
  }

  private function constructPostParams(){
    foreach($_POST as $k => $v){
      $this->paramsPOST[$k] = $v;
    }
  }

  private function constructGetParams(){
     foreach($_GET as $k => $v){
      $this->paramsGET[$k] = $v;
    }
  
  }

}

?>

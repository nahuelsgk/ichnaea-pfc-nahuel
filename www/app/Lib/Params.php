<?php

/*
* This class will handle all the params cleaned from injections
*/

class Params{

  private $paramsGET;
  private $paramsPOST;
  private $json_queried;
  /*
  * Read from queried params and construc both associative arrays
  */
  public function __construct(){
    $this->json_queried = file_get_contents('php://input');
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
    if(isset($this->paramsPOST[$paramName])){
      return $this->paramsPOST[$paramName];
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

  public function setAjaxParams(){
    $json_string = $this->json_queried;
    $json = json_decode($json_string);
    switch(json_last_error()) {
      case JSON_ERROR_NONE:
	//echo ' - Sin errores';
	break;
      case JSON_ERROR_DEPTH:
	echo ' - Excedido tamaño máximo de la pila';
	break;
      case JSON_ERROR_STATE_MISMATCH: 
	echo ' - Desbordamiento de buffer o los modos no coinciden'; 
	break;
      case JSON_ERROR_CTRL_CHAR:
	echo ' - Encontrado carácter de control no esperado';
	break;
      case JSON_ERROR_SYNTAX:
	echo ' - Error de sintaxis, JSON mal formado';
	break;
      case JSON_ERROR_UTF8:
	echo ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
	break;
      default:
	echo ' - Error desconocido';
	break;
    }

    //old method: just to not broke all the eventes systems
    if(isset($json->values)){
      $this->paramsPOST["values"] = $json->values;
      includeLib($json->ajaxDispatch);
      call_user_func($json->function, $this);
    }
    
    //new method: API resolving
    if(isset($json->query)){
      $this->paramsPOST["query"] = $json->query;
      if ($json->ajaxDispatch == 'ASYNC_API'){
        includeLib("Lib/Async_api");
        call_user_func("resolve", $this);
      }
    }
}
}

?>

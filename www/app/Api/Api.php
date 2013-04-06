<?php

namespace Api;

/*
* This class is a handler for the pseudo-api calls
*/
use \Lib\Auth;
use \Api\Project;
use \Api\Matrix;
use \Api\Response;
use \Api\Vars;
class Api{
  
  /*
  * For use this api, you have to be logged. Otherwise must return a error
  */
  private $json_queried;

  public function __construct(){
    //Check if it is a valid query
    $this->validQuery();
    $this->validJson();
    try{
      $this->execute();
    }
    catch(Exception $e){
      printHTML($e->getMessage());
      $api_response = new \Api\Response();
      $api_response->setResponse("KO",\Api\Response::ERROR_UNKNOWN);
    }
  }

  /*
  * Validates the JSON
  */
  private function validJson(){
    $json_string = file_get_contents('php://input');
    $json = json_decode($json_string, true);
        //printHTML("Decoding");
 	//printVar("$json_string");
	//printHTML("End params as string. Start decoding");
	//printVar("---");printVar($json["params"]["public_matrix"]);printVar("---");
	//printHTML("End decoding");

    switch(json_last_error()) {
        case JSON_ERROR_NONE:
          //echo ' - Sin errores';
	  $this->json_queried = $json;
	  break;
	case JSON_ERROR_DEPTH:
	  $this->apiError(' - Excedido tamaño máximo de la pila');
	  break;
	case JSON_ERROR_STATE_MISMATCH:
	  $this->apiError(' - Desbordamiento de buffer o los modos no coinciden');
	  break;
	case JSON_ERROR_CTRL_CHAR:
	  $this->apiError(' - Encontrado carácter de control no esperado');
	  break;
	case JSON_ERROR_SYNTAX:
	  $this->apiError(' - Error de sintaxis, JSON mal formado');
	  break;
	case JSON_ERROR_UTF8:
	  $this->apiError(' - Caracteres UTF-8 malformados, posiblemente están mal codificados');
	  break;
	default:
	  $this->apiError(' - Error desconocido');
	  break;
	}

  }

  /*
  * Validates the query. Actually just checks the cookie to identify the user
  */
  private function validQuery(){
    $session = new \Lib\Auth\Session();
    if(!$session->isValid()) return $this->apiError("Error Cookie");
  }
  
  /*
  * Prints an error of the api
  */
  private function apiError($msg){
    printHTML("ERROR API::: $msg");
  }
 
  /*
  * Prototype for resolving the clases
  */
  private function execute(){
    $uri    = explode("/", $this->json_queried["uri"]);
    $opt    = $this->json_queried["op"];
    $params = $this->json_queried["params"];	
    $return;
    try{
      switch($uri[2]){
        case "project":
	  $project_api = new \Api\Project();
	  $return = $project_api->execute($opt, $params);
          break;
      
      case "matrix":
          $matrix_api = new \Api\Matrix();
          $return = $matrix_api->execute($opt, $params);
	  break;
      
      case "singlevar":
          $var_api = new \Api\SingleVars();
	  $return = $var_api->execute($opt, $params);
	  break;
      
      default:
        throw new ApiException("Uri not valid", ApiException::ERROR_URI_NOT_VALID);
        break;
      }
      $response = new \Api\Response();
      $response->setResponse(false, $return);
      $response->response();
    }
    catch(ApiException $e){
      $e->sendResponseError();
    }
    catch(Exception $e){
      printHTML($e->getMessage());
    }
  }
}
?>

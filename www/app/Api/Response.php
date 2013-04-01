<?php
namespace Api;

class Response{
  
  private $responseJson;

  function __construct(){}
  
  /*
  * Prepares the package
  * - $error: the status of the operation
  * - $array: the associative array that will be passed to encoden to json
  * - $error_code: internal error code: this must be better developed
  *
  * Last update: 15 march 2013
  */
  function setResponse($error = false, $array = NULL, $error = NULL, $message = NULL){
    $this->responseJson = array();
    if($error == true){
      $this->responseJson["status"] = "KO";
      $this->responseJson["error_code"] = isset($error) ? $error : \Api\ApiException::ERROR_UNKNOWN;
      $this->responseJson["default_message"] = isset($message) ? $message : 'No default message';
    }
    else{
      $this->responseJson["status"] = "OK";
      $this->responseJson["data"] = $array;
    }
  }

  function response(){
     header('Content-Type: application/json');
     printHTML("Debug response: ".json_encode($this->responseJson));
     echo json_encode($this->responseJson);
  }
}
?>

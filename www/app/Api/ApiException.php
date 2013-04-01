<?php
namespace Api;

class ApiException extends \Exception {

  //Api concretes error 
  const ERROR_URI_NOT_VALID       = 99900000;
  const ERROR_IN_FORMAT_NOT_VALID = 99900001;

  //Database errors
  const ERROR_DB_SOMEERROR  = 99800000;

  public function __construct($message, $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
  }

  public function sendResponseError(){
    $response = new \Api\Response();
    $response->setResponse(true, null, $this->getCode(), $this->getMessage());
    $response->response();
  }
}

?>

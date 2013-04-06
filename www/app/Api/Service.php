<?php
namespace Api;


class Service{
  
  public function __construct(){
  }

  public function execute($operation, $parameters){
    if (is_callable(array($this, $operation))){
      return call_user_func_array(array($this,$operation), array($parameters));
    }
    else {
      throw new ApiException("Api error: operation don't available: $operation", ApiException::ERROR_FUNCTION_NOT_FOUND);
    }
  }
}
?>

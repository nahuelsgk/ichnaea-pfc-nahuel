<?php
namespace Api;


class Service{
  
  public function __construct(){
  }

  public function execute($operation, $parameters){
    return call_user_func_array(array($this,$operation), array($parameters));    
  }
}
?>

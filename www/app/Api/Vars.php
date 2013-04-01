<?php
namespace Api;

use Domain\Vars as DomainVars;

class Vars extends \Api\Service{
 
  /*
  * Creates vars in the database
  * $vars => [vars["name"]["threshold"]]
  *
  * 1 April 2013
  */
  function create($vars){
    if(!isset($vars)) return;
    $vars_array = $vars["vars"];

    if (is_array(array_shift(array_values($vars_array))) == false) 
      throw new ApiException("Format not valid, must be an array of arrays", ApiException::ERROR_IN_FORMAT_NOT_VALID);

    $var = new DomainVars();
    try{
      $vars_created = $var->create($vars_array);
      return $vars_created;
    }
    catch(\DBiException $e){
      throw new ApiException("Database error", ApiException::ERROR_DB_SOMEERROR);
    }
  }
}
?>

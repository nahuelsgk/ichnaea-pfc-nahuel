<?php
/*
* This class helps the framework to response the Javascript-Json enviroment
*/

class JS{
 
  /*
  * Helper to the return values into JSon 
  * - $ret: associative array with the values to return. 
  *   The structure: "name_of_the_param" => "value of the param"
  */
  public function returnEvent($ret){
    header('Content-Type: application/json');
    echo json_encode($ret);
  }

  /*
  * Helper to the return a redirection  
  * - $path: relative path to redirect 
  */
  public function returnRedirection($path){
    self::returnEvent(array(
      "result"     => "1",
      "operation"  => "redirect",
      "redirectTo" => $path)
    );
  }
}

?>

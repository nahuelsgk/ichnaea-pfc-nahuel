<?php
namespace Api;

use Domain\Matrix as DomainMatrix;
use \Lib\Auth\Session;

class Matrix{
  
  public function __construct(){
  }

  public function execute($operation, $parameters){
    return call_user_func_array(array($this,$operation), array($parameters));    
  }

  /*
  * Matrix service operation: creates a matrix
  * $params:
  *   ["name"]: name of the matrixs
  *   ["public"]: visibility
  *   ["pid"]: belongs to a project
  *
  * Last update: 18 march 2013
  */
  public function create($params){
    $session = new \Lib\Auth\Session();
    $user_id = $session->getUserId();
    printHTML("User id:".$user_id); 
    $matrix = new DomainMatrix();
    $pid = $matrix->newMatrix($params["name"], $user_id, isset($params["pid"]) ? $params["pid"] : NULL);
    $return = array(
      "mid" => $pid
    );
    return $return;
  }
  
  /*
  * Matrix service operation: updates matrix info
  *
  * Last update: 23 march 2013
  */
  public function update($params){
    $matrix = new DomainMatrix();
    $field = array();
    switch($params["field"]){
      case "name":
        $field["name"] = $params["new_value"];
        break;
      case "visibility":
        $value = $params["new_value"];
        $field["public"] = $params["new_value"] == 'public' ? 'y' : 'n';
	break;
    }
    $matrix->updateMatrix($params["mid"], $field);
  }
}
?>

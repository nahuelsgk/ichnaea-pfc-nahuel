<?php
namespace Api;

use Domain\Matrix as DomainMatrix;
use \Lib\Auth\Session;

class Matrix extends \Api\Service{
  
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
  
  public function buildMatrix($params){
    $matrix = new DomainMatrix();
    $matrix_array = array();
    $matrix_array =  $matrix->buildMatrix($params["mid"]);

    $headers = array(
        "vid1" => array(
	  "name"      => "Var static 1",
	  "threshold" => 1500
	),
	"vid2" => array(
	  "name"      => "Var static 2",
	  "threshold" => 2000
	),
	"vid3" => array(
	  "name"      => "Var static 3",
	  "threshold" => 2000
	)

     );
    $headers = $matrix_array;
    $samples = array(
      "sid1" => array(
        "name"   => "Nombre sample 1",
	"values" => array(
	  "m_vid1" => array(
	    "value" => 1000
	  ),
	  array(),
	  "m_vid2" => array(
	    "value" => 2000
	  )
	)
      )   
    );
    $matrixs = array(
      "headers" => $headers,
      "samples" => $samples
    ); 
    return $matrixs;
  }
  
  /*
  * Adds a new sample to matrix
  */
  public function newSample($params){
    $matrix = new DomainMatrix();
    $id = $matrix->addEmptySample($params["mid"]);
    return array("sid" => $id);
  }
}
?>

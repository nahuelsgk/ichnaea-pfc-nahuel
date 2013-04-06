<?php
namespace Api;

use \Domain\Vars as DomainVars;

class SingleVars extends \Api\Service{
 
  /*
  * Creates single vars in the database
  * $vars => [vars["name"]["notes"]["seasons"]=>[seasonObject]]
  *
  * 1 April 2013
  */
  function create($vars){

    //First create the var
    $single_var = new DomainVars\SingleVar();
    $array_of_vars_created = array();
    $id = $single_var->save($vars["name"]);
    $var_element = array("svid" => $id, "name" => $vars["name"]);

    //After creates the season
    printHTML("Saving season");
    foreach($vars["seasons"] as $season_element){
      if(!empty($season_element["name"])){
        $single_var->addSeason($season_element["name"],$season_element["notes"]); 
      }
    }
    printHTML("Final");
    //By now just one var created. In the future several 
    array_push($array_of_vars_created, $var_element);

    return $array_of_vars_created;
  }
}
?>

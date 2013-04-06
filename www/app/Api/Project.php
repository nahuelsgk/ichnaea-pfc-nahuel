<?php
namespace Api;

use Domain\Project as DomainProject;
use \Lib\Auth\Session;

class Project extends \Api\Service{
  
  /*
  * Project service operation: creates a project
  *
  * Last update: 17 march 2013
  */
  public function create($params){
    $project = new DomainProject();
    
    $session = new \Lib\Auth\Session();
    $user_id = $session->getUserId();
    $project_id = $project->newProject($params["name"], $user_id);
    $return = array(
      "pid" => $project_id
    );
    return $return;
  }

  /*
  * Unactive a project
  *
  */
  public function delete($params){
    $project = new DomainProject();
    $project->initProject($params["pid"]);
    $project->updateProject(array('active'=> 'n'));
  }
 
  /*
  * Change the name of a project
  */
  public function updateName($params){
    $project = new DomainProject();
    $project->updateProject(array('name'=>$params['new_name']));
  }
  /*
  * Aggregates a bunch of matrixs to a project
  */
  public function aggregateMatrixs($params){
    $pid         = $params["pid"];
    $set_of_mids = $this->prepareSetMatrixs($params["matrixs_selected"]);
    $project = new DomainProject();
    $project = $project->addSetMatrixs($pid, $set_of_mids);
  }
 
  /*
  * Aggregates a bunch of matrixs to a project
  */
  public function deaggregateMatrixs($params){
    $pid         = $params["pid"];
    $set_of_mids = $this->prepareSetMatrixs($params["matrixs_removed"]);
    $project = new DomainProject();
    $project = $project->remSetMatrixs($pid, $set_of_mids);
  }

  /*
  * Prepares a set of matrixs ids from a JSON object decode array
  */
  private static function prepareSetMatrixs($array){
    $set_matrixs = array();
    foreach($array as $value){
      array_push($set_matrixs, $value["mid"]);
    }
    return $set_matrixs;
  }

}
?>

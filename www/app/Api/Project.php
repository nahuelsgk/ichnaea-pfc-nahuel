<?php
namespace Api;

use Domain\Project as DomainProject;
use \Lib\Auth\Session;

class Project{
  
  public function __construct(){
  }

  public function execute($operation, $parameters){
    return call_user_func_array(array($this,$operation), array($parameters));    
  }

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
  * Project service operation: unactive a projects
  *
  */
  public function delete($params){
    $project = new DomainProject();
    $project->initProject($params["pid"]);
    $project->updateProject(array('active'=> 'n'));
  }

}
?>

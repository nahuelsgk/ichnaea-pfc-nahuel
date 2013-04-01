<?php
includeLib("Domain/Project");
includeLib("Lib/JS");

/*
* Controller for the view "project/new"
* 
* Last update: 3 March 2013
*/
function displayProjectForm($page, $params){
  //Editing an existing project
  $pid = $params->getParam("pid");
  $is_edit = $pid ? true : false;

  // get the matrixs available and used in the project
  $project = new Domain\Project();
  if ($is_edit){ 
    $project->initProject($pid); 
  }

  $matrixs_available = $project->getMatrixs("available");
  $matrixs_included  = $is_edit ? $project->getMatrixs("included") : ''; 
  // If exists a pid param, the user is editing the project info
  $page->assign(array(
    "matrixs_available" => $matrixs_available,
    "matrixs_included"  => $matrixs_included,
    "pid"                => $pid,
    "is_edit"            => $pid ? 'y' : 'n',
    "project_name"       => $pid ? $project->get_name() : '',
  ));

}

/*
* Controller for the view "project/matrix"
*/
function displayProjectMatrixs($page, $params){

  if($params->getParam('submit')){
    //TODO: check_privileges: only project manager
    if ($params->getParam('delete_matrix')) Matrix::disableMatrix($params->getParam('delete_matrix'));
    reloadSafe();
  }
  $pid = $params->getParam('pid');
  $matrixs = Matrix::getMatrixsFromProject($pid);
  $page->assign("matrixs", $matrixs);
}

/*
* Just to display a project. In the future will fulfill all the info needed to display a view for the project
* 
* Last update: 11 march 2013
*/
function displayProjectInfo($page, $params){
  $pid = $params->getParam('pid');

  $project = new Domain\Project();
  $project->initProject($pid);

  $page->assign('project_name', $project->get_name());
  $page->assign('pid', $project->get_id());
}

/*
* Ajax reponse to create a new project
*/
/*function dispatch_newProject($params){
  $values = $params->getParam("values");
  if($values->op == "newProject"){
    $project = new Domain\Project();
    $pid = $project->newProject($values->name, Util::getUserId(),$values->matrixs_selected);
    JS::returnRedirection("/project/edit_new?pid=$pid");
  }
  return;

}*/
/*
* Ajax response for the version 2. All is a prototype
*
* Last udpate: 11 march 2013

function dispatch_deleteProject($pid){
  $project = new Domain\Project();
  $project->initProject($pid);

  //a bit ensambled between layers but if the disable and enable is more complex, will be done on the project class
  $project->updateProject(array('active'=> 'n'));
}
*/

/*
* Ajax reponse to update a project
* - $params->op:
*   - aggregateMatrixs: adds a set of Matrixs into the project
*   - deaggregateMatrixs: removes a set Matrixs from the project
*   - updateName: updates the name of the project. In the future needs a more generic method
* 
* Last updated: 5 march 2013
*/
function dispatch_updateProject($params){
  $values = $params->getParam("values");
  $project = new Domain\Project();
  $project->initProject($values->id);
  
  switch($values->op){

    case "aggregateMatrixs":
      $set_matrixs = prepareSetMatrixs($values->params->matrixs_selected);
      $project->addSetMatrixs($values->id, $set_matrixs);
      break;

    case "deaggregateMatrixs":
      $set_matrixs = prepareSetMatrixs($values->params->matrixs_removed);
      $project->remSetMatrixs($values->id, $set_matrixs);
      break;

    case "updateName":
      $project->updateProject(array("name"=>$values->params->name));
      break;
  }
  return;
}

/*
* Helper to prepare an array of id of matrixs.
* Last update: 3 march 2013
*/
function prepareSetMatrixs($obj_array){
  $set_matrixs = array();
  foreach($obj_array as $value){
    array_push($set_matrixs, $value->mid);
  }
  return $set_matrixs;
}

?>

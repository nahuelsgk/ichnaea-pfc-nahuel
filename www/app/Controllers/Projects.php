<?php
includeLib("Domain/Project");
includeLib("Lib/Auth/SessionSingleton");
includeLib("Lib/JS");

/*
* Controller for the view "project/new"
*/
function displayProjectNewForm($page, $params){
  if ($params->getParam("saveproject")) {
    $project = new Project( 
      array(
        'name' => $params->getParam('name_project'),
	'user_owner' => SessionSingleton::getInstance()->getUserId()
      )
    );

    try{
      $pid = Project::saveProject2(SessionSingleton::getInstance()->getUserId(), $params->getParam('name_project'));
      Util::redirectTo("/matrix/edit_new?pid=".$pid);
    }
    catch(Exception $e){
      printHTML($e->getMessage());
    }
  }
}

/*
* Controller for the view "project/new"
* 
* Last update: 3 March 2013
*/
function displayProjectForm($page, $params){
  //Editing an existing project
  $pid = $params->getParam("pid");
  
  // get the matrixs available and used in the project
  $project = new Project();
  if ($pid){ $project->initProject($pid); }

  $matrixs_available = $project->getMatrixs("available");
  $matrixs_included  = $project->getMatrixs("included"); 
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

function displayProjectName($page, $params){
  $pid = $params->getParam('pid');
  $attrs = Project::getProjectAttributes($pid,array('name'));
  $page->assign('project_name', $attrs);
}

/*
* Ajax reponse to create a new project
*/
function dispatch_newProject($params){
  $values = $params->getParam("values");
  if($values->op == "newProject"){
    $project = new Project();
    $pid = $project->newProject($values->name, Util::getUserId(),$values->matrixs_selected);
    JS::returnRedirection("/project/edit_new?pid=$pid");
  }
  return;

}

/*
* Ajax reponse to update a project
* - $params->op:
*   - aggregateMatrixs: adds a set of Matrixs into the project
*   - deleteMatrixs: removes a set Matrixs from the project
* Last updated: 3 march 2013
*/
function dispatch_updateProject($params){
  $values = $params->getParam("values");
  if($values->op == "aggregateMatrixs"){
    $project = new Project();
    $project->initProject($values->id);
    $set_matrixs = prepareSetMatrixs($values->params->matrixs_selected);
    $project->addSetMatrixs($values->id, $set_matrixs);
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

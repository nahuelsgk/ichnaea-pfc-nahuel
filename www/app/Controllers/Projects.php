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
  printHTML("------------------");
  $page->assign('project_name', $project->get_name());
  $page->assign('pid', $project->get_id());
}

?>

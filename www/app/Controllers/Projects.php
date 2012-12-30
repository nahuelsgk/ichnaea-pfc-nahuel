<?php
includeLib("Domain/Project");
includeLib("Lib/Auth/SessionSingleton");

/*
* Controller for the view "project/new"
*/
function displayProjectNewForm($page){
  global $globalparams;
  if ($globalparams->getParam("saveproject")) {
    $project = new Project( 
      array(
        'name' => $globalparams->getParam('name_project'),
	'user_owner' => SessionSingleton::getInstance()->getUserId()
      )
    );
    $project->saveProject();
  }
}

/*
* Controller for the view "project/matrix"
*/
function displayProjectMatrixs($page){
  global $globalparams;

  if($globalparams->getParam('submit')){
    //TODO: check_privileges: only project manager
    if ($globalparams->getParam('delete_matrix')) Matrix::disableMatrix($globalparams->getParam('delete_matrix'));
    reloadSafe();
  }
  $pid = $globalparams->getParam('pid');
  $matrixs = Matrix::getMatrixsFromProject($pid);
  $page->assign("matrixs", $matrixs);
}

function displayProjectName($page){
  global $globalparams;
  $pid = $globalparams->getParam('pid');
  $rows = Project::getProjectAttributes($pid,array('name'));
  $page->assign('project_name', $rows[0]['name']);
}
?>

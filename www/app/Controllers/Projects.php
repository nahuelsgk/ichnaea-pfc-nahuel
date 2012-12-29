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
  $pid = $globalparams->getParam('pid');
  $matrixs = Matrix::getMatrixsFromProject($pid);
  $page->assign("matrixs", $matrixs);
}
?>

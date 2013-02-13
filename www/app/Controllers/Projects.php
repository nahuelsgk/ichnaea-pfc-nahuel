<?php
includeLib("Domain/Project");
includeLib("Lib/Auth/SessionSingleton");

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
?>

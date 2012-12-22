<?php
includeLib("Domain/Project");
includeLib("Domain/Auth/SessionSingleton");

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
    printVar($project);
  }
}

?>

<?php
includeLib('Domain/Project');
includeLib('Domain/Matrix');
includeLib('Lib/Util');

/*
* Controller to display the page matrix/new
*/
function displayMatrixForm($page, $params){
 if($params->getParam("submit_matrix")){
   $vars       = $params->getParam("variable_name");
   $name       = $params->getParam("name");
   $id_project = $params->getParam("pid");
   $matrix = new Matrix(array(
     "name"       => $name,
     "vars"       => $vars,
     "project_id" => $id_project
   ));
   $matrix->saveMatrix();
 }
}

/*
* Controller to display the page matrix/edit_new. Is the same controller for adding a new matrix and editing it
*/
function displayMatrixForm2($page, $params){
  $edit='n';
  $matrix = array();
  $vars = array();
 
  //If passed a matrix_id as param, we are editing it
  if($mid = $params->getParam('mid')){
    
    //TODO: privileges checking
    try{
      $matrix = Matrix::getMatrixDefinition($mid);
      $vars = Vars::getVarsDefinition($mid);
    }
    catch(Exception $e){
      printHTML($e->getMessage());
      return;
    }
    $edit='y';
  }
  $page->assign("project_name", Project::getProjectAttributes($params->getParam("pid"), array("name")));
  $page->assign("name_matrix", isset($matrix['name']) ?  $matrix['name'] : '');
  $page->assign("vars",        isset($vars) ? $vars : '');
  $page->assign("name_matrix", isset($matrix['name']) ? $matrix['name'] : '');
  $page->assign("is_edit", $edit);
  $page->assign("pid", $params->getParam("pid"));
  $page->assign("mid", $params->getParam("mid"));
}

/*
* Controller for a template to display the basic list of a matrix
* - filters(optional): will display the the matrixs of a concrete project
*/
function displayMatrixsBasicInfoList($page, $params, $filters = NULL){
 if($params->getParam('submit')){
   //TODO: check_privileges: only project manager
   if ($params->getParam('delete_matrix')) Matrix::disableMatrix($params->getParam('delete_matrix'));
   reloadSafe();
 }

 $pid = NULL;
 if (isset($filters)){
   $pid = $params->getParam('pid');
 }
 
 $matrixs = Matrix::getUserMatrixs(Util::getUserId(),$pid);
 $page->assign('matrixs', $matrixs);
 $page->assign('pid', $pid );
}

/*
* Controller for the view matrix/view.
*/
function displayMatrixViewForm2($page, $params){
  //TODO: needs to check if i have permissions
 
  $matrix_id = $params->getParam("mid");
  try{ 
    //Get variables of a matrixs
    $vars = Matrix::getMatrixVars($matrix_id);
  
    //...a complete matrix, even if the values aren't set
    $values = Matrix::getMatrixSamplesValuesComplete($matrix_id);
    
    //..get the matrix project_id
    $pid = Matrix::getMatrixDefinition($matrix_id, array("project_id"));
  }
  catch(Exception $e){
    printHTML($e->getMessage());
  }
  $page->assign('pid',$pid["project_id"]);
  $page->assign('vars',$vars);
  $page->assign('samples',$values);
  $page->assign('mid',$params->getParam("mid"));
}
/*
* AJAX RESPONSES 
*/

/*
* Ajax response for saving a sample
*/
function dispatch_displayMatrixViewForm($params){ 
  $mid    = $params->getParam("mid");
  $values = $params->getParam("values");
  //Fix the values as array of associate array
  $vals_array = array();
  foreach ($values as $val){
    array_push($vals_array,array("id" => $val->id, "value" => $val->value));
  }
  //First create sample
  $ret = Matrix::saveNewSample($mid, $vals_array);
}

/*
* Ajax response for deleting a sample
*/
function dispatch_removeSampleFromTheMatrix($params){
  $mid = $params->getParam("mid");
  $values = $params->getParam("values");
  Matrix::deleteSample($values[0]->id);
}

/*
* Ajax response for adding a matrix
*/

function dispatch_addNewMatrix($params){
  $pid = $params->getParam("pid");
  $values = $params->getParam("values");

  //Fix values as an array of associatived array 
  $variables = array();
  foreach($values->variables as $value){
    $variables[] = array("name" => $value->name, "threshold_limit" => $value->threshold);
  }
  $mid = Matrix::saveNewMatrix($pid, $values->name_matrix, $variables);
  header('Content-Type: application/json');
  echo json_encode(array('mid' => $mid));
}

/*
* Dispatch for massive changes. Actually deprecated. Look for dispatch_updateMatrix2.
*/
function dispatch_updateMatrix($params){
  $mid = $params->getParam("mid");
  $values = $params->getParam("values");
 
  $new_vars = array();
  $delete_vars = array();
  $update_vars = array();

  //Build structures from AJAX to the Domain Functions Requeriments by action
  foreach($values->variables as $value){
    switch($value->action){
      case "new":
        $new_vars[] = array ("name" => $value->name, "threshold_limit" => $value->threshold);
	break;
      case "delete":
        $delete_vars[] = $value->id;
	break;
    }
  }

  try{
    Matrix::updateDefinition($mid,array('name'=>$values->name_matrix));
    Matrix::saveNewVariables($mid, $new_vars);
    Vars::deleteVariables($delete_vars);
  }
  catch(Exception $e){
    printHTML($e->getMessage());
  }


}

function dispatch_updateMatrixVariables($params){
  $mid = $params->getParam("mid");
  $values = $params->getParam("values");
  $update = get_object_vars($values);
  Vars::updateVar($update);

}

/*
* Ajax response: update or adds the values of matrix
*/
function dispatch_updateMatrixValues($params){
  $values = $params->getParam("values");
  try{
    if (!empty($values->valid)){Values::updateValue($values->valid, $values->value);}
    elseif (empty($values->valid)){Values::saveValues($values->sample_id,array(array("id"=>$values->vid, "value"=>$values->value)));}
  }
  catch (Exception $e){
    printHTML($e->getMessage());
  }
}

/*
* Ajax response: add a sample to the matrix
*/
function dispatch_addSample($params){
  $values = $params->getParam("values");
  Matrix::saveNewSample($values->mid, NULL);
}
?>

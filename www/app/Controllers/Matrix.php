<?php
includeLib('Domain/Matrix');
includeLib('Lib/Util');

/*
* Controller to display the page matrix/new
*/
function displayMatrixForm($page){
 global $globalparams;   
 
 if($globalparams->getParam("submit_matrix")){
   $vars       = $globalparams->getParam("variable_name");
   $name       = $globalparams->getParam("name");
   $id_project = $globalparams->getParam("pid");
   $matrix = new Matrix(array(
     "name"       => $name,
     "vars"       => $vars,
     "project_id" => $id_project
   ));
   $matrix->saveMatrix();
 }
}

/*
* Controller to display the page matrix/edit_new.
* Displays the info of a matrix
*/
function displayMatrixForm2($page){
  global $globalparams;
  $edit='n';
  $matrix = array();
  $vars = array();
  if($mid = $globalparams->getParam('mid')){
    
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

  $page->assign("name_matrix", isset($matrix['name']) ?  $matrix['name'] : '');
  $page->assign("vars",        isset($vars) ? $vars : '');
  $page->assign("name_matrix", isset($matrix['name']) ? $matrix['name'] : '');
  $page->assign("is_edit", $edit);
  $page->assign("pid", $globalparams->getParam("pid"));
  $page->assign("mid", $globalparams->getParam("mid"));
}

/*
* Controller for a template to display the basic list of a matrix
* - filters: if is set the param, will display the the matrixs of a concrete project
*/
function displayMatrixsBasicInfoList($page, $filters = NULL){
 global $globalparams;
 if($globalparams->getParam('submit')){
   //TODO: check_privileges: only project manager
   if ($globalparams->getParam('delete_matrix')) Matrix::disableMatrix($globalparams->getParam('delete_matrix'));
   reloadSafe();
 }

 $pid = NULL;
 if (isset($filters)){
   $pid = $globalparams->getParam('pid');
 }
 
 $matrixs = Matrix::getUserMatrixs(Util::getUserId(),$pid);
 $page->assign('matrixs', $matrixs);

}

function displayMatrixViewForm($page){
  global $globalparams;
  //TODO: needs to check if i have permissions
 
  $matrix_id = $globalparams->getParam("mid");

  //Get variables of a matrixs
  $vars = Matrix::getMatrixVars($matrix_id);
  
  $samples = Matrix::getMatrixSamples($matrix_id);
  
  $values = Matrix::getMatrixSamplesValues($matrix_id);
  $matrix = array();
  //Init the matrix
  foreach($samples as $sample){
    foreach($vars as $var){
      $matrix[$sample["id"]][$var["id"]] = '';
    }
  }
  //Full fill values
  foreach($values as $value){$matrix[$value["sample_id"]][$value["var_id"]] = $value["value"];}
  
  $page->assign('vars',$vars);
  $page->assign('samples',$matrix);
  $page->assign('mid',$globalparams->getParam("mid"));
}

function displayMatrixViewForm2($page){
  global $globalparams;
  //TODO: needs to check if i have permissions
 
  $matrix_id = $globalparams->getParam("mid");
  try{ 
    //Get variables of a matrixs
    $vars = Matrix::getMatrixVars($matrix_id);
  
    $samples = Matrix::getMatrixSamples($matrix_id);
  
    $values = Matrix::getMatrixSamplesValuesComplete($matrix_id);
  
    //Init the matrix
    $pid = Matrix::getMatrixDefinition($matrix_id, array("project_id"));
  }
  catch(Exception $e){
    printHTML($e->getMessage());
  }
  $page->assign('pid',$pid["project_id"]);
  $page->assign('vars',$vars);
  $page->assign('samples',$values);
  $page->assign('mid',$globalparams->getParam("mid"));
}
/*
* AJAX RESPONSES 
*/

/*
* Ajax response for saving a sample
*/
function dispatch_displayMatrixViewForm(){ 
  global $globalparams;

  $mid    = $globalparams->getParam("mid");
  $values = $globalparams->getParam("values");
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
function dispatch_removeSampleFromTheMatrix(){
  global $globalparams;
  $mid = $globalparams->getParam("mid");
  $values = $globalparams->getParam("values");
  printVar($values[0]->id); 
  Matrix::deleteSample($values[0]->id);
}

/*
* Ajax response for adding a matrix
*/

function dispatch_addNewMatrix(){
  global $globalparams;
  $pid = $globalparams->getParam("pid");
  $values = $globalparams->getParam("values");
  printVar($values);
  //Fix values as an array of associatived array 
  $variables;
  foreach($values->variables as $value){
    $variables[] = array("name" => $value->name, "threshold_limit" => $value->threshold);
  }
  Matrix::saveNewMatrix($pid, $values->name_matrix, $variables);
}

/*
* Dispatch for massive changes. Actually deprecated. Look for dispatch_updateMatrix2.
*/
function dispatch_updateMatrix(){
  global $globalparams;
  $mid = $globalparams->getParam("mid");
  $values = $globalparams->getParam("values");
  printVar($values);
 
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

function dispatch_updateMatrixVariables(){
  global $globalparams;
  $mid = $globalparams->getParam("mid");
  $values = $globalparams->getParam("values");
  $update = get_object_vars($values);
  Vars::updateVar($update);

}
?>

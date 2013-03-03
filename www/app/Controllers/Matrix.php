<?php
includeLib('Domain/Project');
includeLib('Domain/Matrix');
includeLib('Lib/Util');

/*
* Controller for page matrix/edit_new. Adds and edit in the same page
*/
function pageMatrixDefinition($page, $params){
  $values = array();
  $edit='n';
  $matrix = array();
  $vars = array();
 
  //default values for new matrixs
  $values["public_matrix"] = 'n';

  //If passed a matrix_id as param, we are editing it
  if($mid = $params->getParam('mid')){
    
    $matrix = Matrix::getMatrixDefinition($mid);
      
    $values["public_matrix"] = $matrix["public"];
    $values["matrix_name"] = $matrix['name'];
    $vars = Vars::getVarsDefinition($mid);
    $edit='y';
  }

  $page->assign($values);
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
function displayMatrixsList($page, $params, $filters = NULL){
 $matrixs = Matrix::getMatrixs();
 $page->assign('matrixs', $matrixs);
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
 
  //$new_vars = array();
  //$delete_vars = array();
  //$update_vars = array();

  //Build structures from AJAX to the Domain Functions Requeriments by action
  /*foreach($values->variables as $value){
    switch($value->action){
      case "new":
        $new_vars[] = array ("name" => $value->name, "threshold_limit" => $value->threshold);
	break;
      case "delete":
        $delete_vars[] = $value->id;
	break;
    }
  }
  */
  try{
    Matrix::updateDefinition($mid,array(
      'name'=>$values->name_matrix, 
      'public'=>$values->visibility == 'public' ? 'y' : 'n'
      )
    );
    //Matrix::saveNewVariables($mid, $new_vars);
    //Vars::deleteVariables($delete_vars);
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
* Ajax response: disables a matrix
*/
function dispatch_disableMatrix($params){
  $values = $params->getParam("values");
  Matrix::disableMatrix(array($values->mid));
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

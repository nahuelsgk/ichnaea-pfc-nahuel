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

function displayMyMatrixs($page){
  global $globalparams;
  $matrixs = Matrix::getUserMatrixs(Util::getUserId());
  $page->assign('matrixs', $matrixs);
}

function displayMatrixViewForm($page){
  global $globalparams;
  //TODO: needs to check if i have permissions
  
  //Get variables of a matrixs
  $vars = Matrix::getMatrixVars($globalparams->getParam("mid"));
  //Fix vars
  $columns = array();
  foreach ($vars as $v){ $columns[$v["id"]] = $v["name"];};

  $samples = Matrix::getMatrixSamples($globalparams->getParam("mid"));
  $rows = array();
  foreach ($samples as $s){ $rows[$s["id"]] = $s["id"]; }
  
  $values = Matrix::getMatrixSamplesValues($globalparams->getParam("mid"));
  $matrix = array();
  foreach($values as $value){$matrix[$value["sample_id"]][$value["var_id"]] = $value["value"];}
  
  $page->assign('vars',$vars);
  $page->assign('samples',$matrix);
  $page->assign('mid',$globalparams->getParam("mid"));
}


function dispatch_displayMatrixViewForm(){ 
  printHTML("Ajax controller");
  global $globalparams;

  $mid    = $globalparams->getParam("mid");
  $values = $globalparams->getParam("values");
  //Fix the values as array of associate array
  $vals_array = array();
  foreach ($values as $val){
    array_push($vals_array,array("id" => $val->id, "value" => $val->value));
  }
  //First create sample
  Matrix::saveNewSample($mid, $vals_array);
  //Insert all values
}
?>

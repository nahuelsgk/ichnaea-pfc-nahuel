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
?>

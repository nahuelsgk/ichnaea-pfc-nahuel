<?php
includeLib('Domain/Matrix');

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
?>

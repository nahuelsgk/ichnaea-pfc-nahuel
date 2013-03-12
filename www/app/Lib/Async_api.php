<?

/*
* Prototype for an API. Main event handler
*
* Last update: 2013
*/
function resolve($params){
  
  $values = $params->getPostParam("query");
  $query = json_decode($values, TRUE);


  //Resolve wich controller we must include
  $controller = decodeController($query["op"]);
  includeLib($controller);

  //Call the function
  call_user_func_array("dispatch_".$query["op"], $query["params"]);

}

/*
* By now just one controller for this API
* Last update: 11 march 2013
*/
function decodeController($op_requested){
  $decoded_functions = array(
    "deleteProject" => "Controllers/Projects"
  );
  return $decoded_functions[$op_requested];
}
?>

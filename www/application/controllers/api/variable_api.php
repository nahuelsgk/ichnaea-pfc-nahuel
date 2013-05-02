<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Variable_api extends REST_Controller{
  
	function variable_get(){
	  
	  $this->load->model('variable_model');
	
	  if(!$this->get('id'))
	  {
	    $list = $this->variable_model->get_all();
	    $this->response(array('msg' => 'Loading list of variables', 'list' => $list), 200);
	  }
	  else 
	  {		  
	  	$id = $this->get('id');
	    $variable = $this->variable_model->get($id);
	    $this->response(array('msg' => 'Loading variable', 'data' => $variable), 200);
	  }
	  
	  $this->response(NULL, 400);
	}
	
	function variable_put(){
	  if(!$this->get('id'))
	  {
	    $name 			= $this->put("name");
	    $description 	= $this->put("description");
	    if(empty($name)) $this->response("Needs at least a name", 400);
	  
	    $this->load->model('variable_model');
	    $id = $this->variable_model->create($name, $description);
	    $this->response(array('msg' => 'Variable '.$name.' created', 'id' => $id), 200);
	  }
	  else{
	  	$id 			= $this->get("id");
	    $name 			= $this->put("name");
	    $description 	= $this->put("description");
	    if(empty($name)) $this->response("Needs at least a name", 400);
	    $this->load->model('variable_model');
	    $id = $this->variable_model->update($id, $name, $description);
	    $this->response(array('msg' => 'Variable updated'), 200);
	  }
   	}
	
}
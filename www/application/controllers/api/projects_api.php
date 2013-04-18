<?php defined('BASEPATH') OR exit('No direct script access allowed');


require APPPATH.'/libraries/REST_Controller.php';

class Projects_api extends REST_Controller{
	
	function projects_put(){
		//It's a unsecure api by now, the user will be in the object passed until get know
		//how to securize the api
		$name    = $this->put('name');
		$creator = $this->put('creator');
		
		//Check inputs
		if(empty($name) || empty($creator)) $this->response(array('msg'=>'Projects needs at least a name'), 400);
		
		//Create a project
		$this->load->model('project_model');
		$id_project = $this->project_model->create($name, $creator);
		$this->response(array('msg'=>'Project created', 'id' => $id_project, 'name' => $name), 200);
	}
}
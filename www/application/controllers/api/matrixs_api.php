<?php defined('BASEPATH') OR exit('No direct script access allowed');


require APPPATH.'/libraries/REST_Controller.php';

class Matrixs_api extends REST_Controller{
	
	function matrixs_put(){
		$name = $this->put('name');
		
		if(empty($name)) $this->response(array('msg'=>"Matrix's need a name"), 400);
		
		$this->load->model('matrix_model');
		$id = $this->matrix_model->create($name);
		$this->response(array('msg'=>'Matrix created', 'id'=>$id), 200);
	}
	
	function matrixs_post(){
		$matrix_id = $id = $this->get('id');
		if(empty($matrix_id)) $this->response(array('msg' => 'Need a matrix id to perform operation'), 400);
		
		//Add a single var configuration
		$season_id = $this->post('season_id');
		$single_var_id = $this->post('svid');
		$this->load->model('variables_configuration/single_variables_configuration_model');
		$configuration_id = $this->single_variables_configuration_model->add($matrix_id, $season_id, $single_var_id);
		$this->response(array('configuration_id' => $configuration_id, 'msg' => 'Single variable added with season'), 200);
	}
}
?>
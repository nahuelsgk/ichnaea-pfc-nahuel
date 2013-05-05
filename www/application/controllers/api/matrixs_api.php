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
	
	function variable_put(){
		 if($this->get('id')){
		 	$id = $this->get('id');
		 	$this->load->model('matrix_model');
		 	$this->matrix_model->createColumn($id);
		 }
		 //Must return the id
		 $this->response(array('msg'=> "Succesful column created"), 200);
	}
	
	function content_get(){
		if($this->get("id")){
			$id = $this->get("id");
			$this->load->model('matrix_model');
			$document = $this->matrix_model->getDocument($id);
			$this->response(array('data'=>$document[0], 'msg'=>'Matrix loaded successfully'), 200);
		}
	}
	
	function values_put(){
		if($this->get("id")){
			$id     = $this->get("id");
			$row    = $this->get("row");
			$column = $this->get("column");
			$value  = $this->put("value");

			$this->load->model('matrix_model');
			$this->matrix_model->updateValue($id, $row, $column, $value);
			$this->response(array('msg' => 'Value updated successfully'), 200);
		}
	}
	
	function sample_put(){
		if($this->get("id")){
			$id = $this->get("id");
			$this->load->model('matrix_model');
			$this->matrix_model->createSample($id);
		}
	}
	
}
?>
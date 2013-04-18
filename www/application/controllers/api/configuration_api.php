<?php defined('BASEPATH') OR exit('No direct script access allowed');


require APPPATH.'/libraries/REST_Controller.php';

class Configuration_api extends REST_Controller{
	
	function configuration_single_variables_get(){
		$matrix_id = $this->get('matrix_id');
		
		if(empty($matrix_id)) return $this->response(array('msg' => 'I need a matrix', 400));
		
		$this->load->model('variables_configuration/single_variables_configuration_model');
		$list = $this->single_variables_configuration_model->get_all_from_matrix($matrix_id);
		$this->response(array('list' => $list, 'msg' => 'Loading single variables added in this matrixs'), 200);
	}
	
}
?>
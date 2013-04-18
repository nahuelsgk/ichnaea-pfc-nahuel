<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Single_variable_api extends REST_Controller{
	
	function single_variable_get(){
		$this->load->model('single_variable_model');
		
		$id=$this->get('id');
		if(!empty($id)){
			$single_variable_array = $this->single_variable_model->get_complete($id);
			$this->response(array('msg' => "Loading single variable", 'list' => $single_variable_array), 200);	
		}
		else{
			$type = $this->get('type');
			if($type=="simple"){
				$list = $this->single_variable_model->get_all();
				$this->response(array('list' => $list, 'msg' => "Listing single variables' system"), 200);
			}
			else{
				$list = $this->single_variable_model->get_all_complete_list();
				$this->response(array('list' => $list, 'msg' => "Listing single variables' system"), 200);
			}
		}
	}
	
	function single_variable_put(){
	    $this->load->model('single_variable_model');
	    
	    $name = $this->put('name');
	    if(empty($name)) $this->response(array('msg'=>'At least needs a name'), 400);
	    
	    $id = $this->single_variable_model->create($name);
	    
	    $this->load->model('season_model');
	    $seasons = $this->put('seasons');
	    foreach($seasons as $season){
	    	$this->season_model->create($id, $season['name'], $season['notes']);
	    }
	    $this->response(array('msg'=>'Variable created'), 200);
	}
	
}

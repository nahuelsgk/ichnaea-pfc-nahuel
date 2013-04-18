<?php defined('BASEPATH') OR exit('No direct script access allowed');


require APPPATH.'/libraries/REST_Controller.php';

class Seasons_api extends REST_Controller{
	
	function season_get(){
		if(!$this->get('svid'))
        {
        	//Return all the the season of the system but actually...
        	$this->response(NULL, 400);
        }
        else{
        	$single_var_id = $this->get('svid');
        	$this->load->model('season_model');
        	$list = $this->season_model->get($single_var_id);
        	$this->response(array('list' => $list, 'msg' => 'Seasons from '.$single_var_id.' loaded'));
        	
        }
	}
}
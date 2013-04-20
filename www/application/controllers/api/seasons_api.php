<?php defined('BASEPATH') OR exit('No direct script access allowed');


require APPPATH.'/libraries/REST_Controller.php';

class Seasons_api extends REST_Controller{
	
	function season_get(){	
		if($this->get('id')){
		 $this->load->model('season_model');
		 $id = $this->get('id');
		 $season = $this->season_model->get($id);
		 $this->response(array('data' => $season, 'msg' => 'Loading season'), 200);
		}
		
		else{
		 $this->load->model('season_model');
		 $seasonList = $this->season_model->get();
		 $this->response(array('list' => $seasonList, 'msg' => 'Loading season list'), 200);
		}
	}
	
	function season_put(){
	  if($this->get('id')){
	  	$name = $this->put('name');
	    if(empty($name)) $this->response(array('msg' => 'At least, season needs a name', 400));
	    $this->load->model('season_model');
	    $name 	    = $this->put('name');
	    $notes 	    = $this->put('notes');
	    $start_date = $this->put('start_date');
	    $end_date   = $this->put('end_date');
	    $content	= $this->put('content');
	    $id 		= $this->get('id');
	    $this->season_model->update($id, $name, $notes, $start_date, $end_date, $content);
	    $this->response(array('msg' => 'Season updated'), 200);
	  }
	  else{
	    $name = $this->put('name');
	    if(empty($name)) $this->response(array('msg' => 'At least, season needs a name', 400));
	    $this->load->model('season_model');
	    $name 	  = $this->put('name');
	    $notes 	  = $this->put('notes');
	    $start_date = $this->put('start_date');
	    $end_date   = $this->put('end_date');
	    $content	  = $this->put('content');
	    $this->season_model->create($name, $notes, $start_date, $end_date, $content);
	    $this->response(array('msg' => 'Season created'), 200);
	  }
	}
}
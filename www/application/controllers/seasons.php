<?php 

class Seasons extends MY_Controller{
	
	function index(){
		$this->render($this->load->view('Seasons/Admin', null, true));
	}
	
}
?>
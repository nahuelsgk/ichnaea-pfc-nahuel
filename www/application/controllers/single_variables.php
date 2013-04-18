<?php

class Single_variables extends MY_Controller{

	function index(){
		//Just print the view. This view is an AJAX fulfilling
		$this->render($this->load->view('SingleVariable/List', null, true));	
	}
}
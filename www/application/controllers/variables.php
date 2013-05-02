<?php

class Variables extends MY_Controller{

	function index(){
		//Just print the view. This view is an AJAX fulfilling
		$this->render($this->load->view('Variable/List', null, true));	
	}
}
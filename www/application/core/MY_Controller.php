<?php
session_start();
class MY_Controller extends CI_Controller{
	
	function __construct()
	{
		parent::__construct();
    	if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['login'];
			$data['user_id'] = $session_data['id'];
		
		}
    	else
	   	{
		   //If no session, redirect to login page
		   redirect('login', 'refresh');
		}
	
    }	
    
    protected $layout = 'Layout/layout';
  	protected function render($content) {
    	$view_data = array(
     	'content' => $content
    );
    
    $this->load->view($this->layout,$view_data);
  }
    
    
}

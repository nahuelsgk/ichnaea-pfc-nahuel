<?php 
class project extends MY_Controller{

	public function __construct()
	{
		parent::__construct();
	}
    
	public function index()
	{
		$this->load->model('project_model');
		$session_data = $this->session->userdata('logged_in');
		$data['user_id'] = $session_data['id'];
	    $data['projects'] = $this->project_model->get_user_projects($data['user_id']);
		$this->render($this->load->view('Project/UserProjects', $data, true));
	}
		
	/* 
	 * View for project configuration
	 */
    function view($id){
    	
    	//Loads the projects
    	$this->load->model('project_model');
		$data['project'] = $this->project_model->get_project($id);
		
		//Project don't exists
		if (empty($data['project'])){
		  show_404();
	    }
	    
	    //Load available matrixs for the project 
		$data['matrixs_available'] = $this->project_model->get_available_matrixs($id);
		
		//Load included matrixs for the project
		$data['matrixs_included'] = $this->project_model->get_included_matrixs($id);
		
		//Display
		$content = $this->load->view('Project/ProjectView', $data, true);
		$this->render($content);
		
	}
	
	/*
	 * Logout. We must move it to another controller
	 */
	function logout()
	{
	   $this->session->unset_userdata('logged_in');
	   session_destroy();
	   redirect('login', 'refresh');
	 }
	 
}
?>

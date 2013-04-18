<?php
class login extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
		
	//MUST FIX THEMING in CODEIGNITER
	public function index(){
		$this->load->helper(array('form'));
		$this->load->view('templates/header');
		$this->load->view('Login/LoginSigninView');
		$this->load->view('templates/footer');
	}
	
	public function verifylogin(){
	//This method will have the credentials validation
	   $this->load->library('form_validation');
	
	   $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
	   $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
	   $this->form_validation->set_error_delimiters('<div style="color: #e11">', '</div>');
	   if($this->form_validation->run() == FALSE)
	   {
	     //Field validation failed.&nbsp; User redirected to login page
	     $this->load->view('templates/header');
	     $this->load->view('Login/LoginSigninView');
	     $this->load->view('templates/footer');
	   }
	   else
	   {
	     //Go to private area
	     redirect('project', 'refresh');
	   }
	 }

	 
	
	function signin(){	 	
	   //This method will have the credentials validation
	   $this->load->library('form_validation');
	
	   $this->form_validation->set_rules('email_signin',    'Username', 'trim|required|xss_clean|callback_check_user');
	   $this->form_validation->set_rules('password_signin', 'Password', 'trim|required|xss_clean');
	   $this->form_validation->set_error_delimiters('<div style="color: #e11">', '</div>');
	   if($this->form_validation->run() == FALSE)
	   {	$this->load->view('templates/header');
			$this->load->view('Login/LoginSigninView');
			$this->load->view('templates/footer');
	   }
	   else
	   {
	   	 $name  = $this->input->post('username_signin');
	   	 $email = $this->input->post('email_signin');
	   	 $paswd = $this->input->post('password_signin');
	   	 $this->load->model('user_model');
	   	 $result = $this->user_model->create($email, $name, $paswd);
	     redirect('/signin/created');
	   }
	 	
	 }

	 function check_user($email)
	 {
	   $this->load->model('user_model');
	   $isAlreadyAUser = $this->user_model->exists($email);
	   if(!$isAlreadyAUser){
	     return true;
	   }
	   else
	   {
	     $this->form_validation->set_message('check_user', 'Already a user. Forget your password?');
	     return false;
	   }
	 }
	 
     function check_database($password)
	 {
	   $username = $this->input->post('username');
	
	   $this->load->model('user_model');
	   
	   //query the database
	   $result = $this->user_model->login($username, $password);
	
	   if($result)
	   {
	     $sess_array = array();
	     foreach($result as $row)
	     {
	       $sess_array = array(
	         'id' => $row->id,
	         'login' => $row->login
	       );
	       $this->session->set_userdata('logged_in', $sess_array);
	     }
	     return TRUE;
	   }
	   else
	   {
	     $this->form_validation->set_message('check_database', 'Invalid username or password');
	     return false;
	   }
	 }
	function created(){
		$this->load->view('Login/AccountCreated');
	}
}
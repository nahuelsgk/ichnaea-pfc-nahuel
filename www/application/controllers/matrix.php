<?php
class matrix extends MY_Controller{
	
	//List all the system's matrixs
	public function index(){
		$this->load->model('matrix_model');
		$data['matrixs'] = $this->matrix_model->get_all_matrixs();
		$this->render($this->load->view('Matrix/SystemList', $data, true));	
	}
	
	//View to set the matrix configuration
	public function configuration($id){
		$this->load->model('matrix_model');
		$data['matrix'] = $this->matrix_model->get($id);
		$this->render($this->load->view('Matrix/Configuration', $data, true));
	}

	public function view($id){
		$this->render($this->load->view('Matrix/View', null, true));
	}
    
	public function view2($id){
		$this->render($this->load->view('Matrix/View2', null, true));
	}
}
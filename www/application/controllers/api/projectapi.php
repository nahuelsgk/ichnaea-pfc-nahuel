<?php defined('BASEPATH') OR exit('No direct script access allowed');


require APPPATH.'/libraries/REST_Controller.php';

class Projectapi extends REST_Controller{
	
	function project_put(){
		switch($this->put('op')){
			case "name":
				$update_name 	= $this->put('name');
				$pid			= $this->get('id');
				$this->load->model('project_model');
				$this->project_model->update_name($pid, $update_name);
				break;
		
			case "addMatrixs":
				$matrixs		= $this->put('set');
				$this->load->model('project_model');
				$this->project_model->add_matrixs($matrixs);
				break;
			
			case "removeMatrixs":
				$matrixs 		= $this->put('set');
				$this->load->model('project_model');
				$this->project_model->remove_matrixs($matrixs);
				break;
				
			default:
				$this->response(array('msg' => 'Project api operation failed: '.$this->put('op')), 400);	
				return;
				break;	
			
		}
		$this->response(array('msg'=>'Changes saved'), 200);
	}
	
	function project_get(){
	  if(!$this->get('id'))
        {
        	$this->response(NULL, 400);
        }

        // $user = $this->some_model->getSomething( $this->get('id') );
    	$users = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!', array('hobbies' => array('fartings', 'bikes'))),
		);
		
    	$user = @$users[$this->get('id')];
    	
        if($user)
        {
            $this->response($user, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'User could not be found'), 404);
        }
	}
	
	function project_post(){
		//responds with an update project
		echo 'POST restul api codeigniter';
		$this->response(array('msg'=>"OK"), 200);
	}
	
	function project_delete(){
		
		$project_id = $this->get('id');
		if(empty($project_id)) $this->response(array('msg'=>'Needs at least a project id'), 404);
		
		$this->load->model('project_model');
		$this->project_model->disable($project_id);
	    $this->response(array('msg'=>"Project disabled", "id" => $project_id), 200);
    }
	
    public function send_post()
	{
		var_dump($this->request->body);
	}


	public function send_put()
	{
		var_dump($this->put('foo'));
	}
}
?>
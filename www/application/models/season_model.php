<?php 
class season_model extends CI_Model{
	
	function __construct(){
		$this->load->database();
	}
	
	function get($id = null){
		if(isset($id)){
			$query = $this->db->get_where('season', array('id'=>$id));
			return $query->result();
		}	
		else{
			$query = $this->db->get('season');
			return $query->result();	
		}
	}
	
	function create($name, $notes, $start_date, $end_date, $content){
		$this->db->insert('season', array(
		 'name'       => $name, 
		 'notes'      => $notes , 
		 'start_date' => '2012-01-31', 
		 'end_date'   => '2012-12-01', 
		 'content'    => $content
		));
	}
	
	function update($id, $name, $notes, $start_date, $end_date, $content){
		$this->db->update('season', array(
		 'name'       => $name, 
		 'notes'      => $notes , 
		 'start_date' => '2012-01-31', 
		 'end_date'   => '2012-12-01', 
		 'content'    => $content),
		 array('id' => $id));
	}
	
}
?>
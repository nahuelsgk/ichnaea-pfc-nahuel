<?php
class matrix_model extends CI_Model{
	function __construct(){
		$this->load->database();
	}
	
	function get_all_matrixs(){
		$query = $this->db->get('matrix');
		return $query->result(); 
	}
	
	function create($name){
		$this->db->insert('matrix', array('name' => $name));
		return $this->db->insert_id();
	}
	
	function get($id){
	 	$query = $this->db->get_where('matrix', array('id'=>$id));
	 	return $query->row_array();
	}
}
<?php

class Variable_model extends CI_Model{
	public function __construct()
	{
	  $this->load->database(); 
	}
	
	public function get_all(){
	  $query = $this->db->get('variable');
	  return $query->result();
	}
	
	public function get($id){
	  $query = $this->db->get_where('variable', array('id' => $id));
	  return $query->result();
	}
	
	public function create($name, $description){
	   $this->db->insert('variable', array('name'=>$name, 'description' => $description));
	   return $this->db->insert_id();
	}
	
	public function update($id, $name, $description){
		$this->db->where(array('id' => $id));
		$this->db->update('variable', array('name' => $name, 'description' => $description));	
	}
}
?>
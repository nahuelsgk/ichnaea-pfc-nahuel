<?php 
class season_model extends CI_Model{
	
	function __construct(){
		$this->load->database();
	}
	
	function get($svid){
		$query = $this->db->get_where('season', array('single_var_id' => $svid));
		return $query->result();	
	}
	
	function create($svid, $name, $notes){
		$this->db->insert('season', array('single_var_id' => $svid, 'name' => $name, 'notes' => $notes));
	}
	
}
?>
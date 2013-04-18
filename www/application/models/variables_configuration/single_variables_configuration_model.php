<?php
class Single_variables_configuration_model extends CI_Model{
	
	function __construct(){
		$this->load->database();
	}
	
	function add($matrix_id, $season_id, $single_var_id){
		$query = $this->db->insert('var_configuration', array('mid' => $matrix_id));
		$conf_id = $this->db->insert_id();
		
		$this->db->insert('var_configuration_single_var', array(
			'var_conf_id' => $conf_id, 
			'mid' => $matrix_id, 
			'svid'=> $single_var_id, 
			'season_id' => $season_id));
		return $this->db->insert_id();
	}
		
	function get_all_from_matrix($matrix_id){
		 $this->db->select(
		 	'vcsv.var_conf_id column_id,  vcsv.id as configuration_id_single_var ,sv.name as single_var_name, s.id as season_id, s.name as season_name, 
		 		s.notes as season_notes 
		 	from var_configuration_single_var vcsv 
		 		inner join season s ON vcsv.season_id = s.id 
		 		inner join single_var sv ON vcsv.svid = sv.id 
		 	where mid = '.$matrix_id, FALSE);
		 $query = $this->db->get();
		 return $query->result();
	}

}
<?php
class single_variable_model extends CI_Model{
	
	function __construct(){
		$this->load->database();
	}
	
	function get_all(){
		$query = $this->db->get('single_var');
		return $query->result();
	}
	
	function create($name){
		$this->db->insert('single_var', array('name' => $name));
    	return $this->db->insert_id();
	}

	/*
	 * Builds a complex structure like this
	   
	   [id_of_single_var: 54] => Array
        (
        	[id_of_single_var: 54] => Array
            [name] => A new variable with seasons
            [seasons] => Array
                (
                    [season_id: 23] => Array
                        (
                            [season_name] => Winter
                            [season_notes] => in Nairobi
                        )

                    [24] => Array
                        (
                            [season_name] => Winter
                            [season_notes] => in Summer
                        )

                )

        )
	*/
	function get_all_complete_list(){
		//select * from single_var sv INNER JOIN season s ON sv.id = single_var_id ;
		$this->db->select('sv.*');
		$this->db->select('s.id AS season_id, s.name AS season_name, s.notes AS season_notes', FALSE);
		$this->db->from('single_var sv');
		$this->db->join('season s', "sv.id = s.single_var_id");
		$query = $this->db->get();
		return $this->build_single_variables_array($query->result_array());
		
	}
	
	function get_complete($id){
		$this->db->select('sv.*');
		$this->db->select('s.id AS season_id, s.name AS season_name, s.notes AS season_notes', FALSE);
		$this->db->from('single_var sv');
		$this->db->join('season s', "sv.id = s.single_var_id");
		$this->db->where('sv.id', $id);
		$query = $this->db->get();
		return $query->result_array();
		
	}
	
	function build_single_variables_array($array_records){
		$final_array=array();
		foreach($array_records as $variable_per_season){
			$final_array[$variable_per_season['id']]['name'] = $variable_per_season['name'];
			$final_array[$variable_per_season['id']]['id'] = $variable_per_season['id'];
			
			if(isset($variable_per_season['season_id'])){
				if(!isset($final_array[$variable_per_season['id']]['seasons'])) $final_array[$variable_per_season['id']]['seasons'] = array(); 
				array_push($final_array[$variable_per_season['id']]['seasons'], array(
																	'season_id'	   =>$variable_per_season['season_id'],
																	'season_name'  =>$variable_per_season['season_name'],
																	'season_notes' =>$variable_per_season['season_notes'],
																	));
			}
		}
		return array_values($final_array);
	}
}
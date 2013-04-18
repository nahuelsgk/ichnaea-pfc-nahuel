<?php
class Project_model extends CI_Model{
  
	function __construct()
    {
        $this->load->database();
    }
    
    
    /*
     * Creators 
     */
    function create($name, $creator){
    	$this->db->insert('projects', array('name' => $name, 'creator' => $creator));
    	return $this->db->insert_id();
    }
    
    /*
     * Getters 
     */
    function get_all_projects()
    {	
        $query = $this->db->get('projects');
        return $query->result();
    }
    
    function get_project($id){
    	$query = $this->db->get_where('projects', array('id' => $id));
    	return $query->row_array();
    }
    
    function get_user_projects($user_id){
    	$query = $this->db->get_where('projects', array('creator'=>$user_id, 'active'=>'y'));
    	return $query->result();   
    }
    
    function get_available_matrixs($id){
    	$this->db->from('matrix');
    	$this->db->where("id NOT IN (SELECT matrix_id FROM project_matrix WHERE project_id = $id)");
    	$query = $this->db->get();
    	return $query->result();
    }
    
    function get_included_matrixs($id){
    	$this->db->from('matrix');
    	$this->db->join('project_matrix','matrix.id = project_matrix.matrix_id', 'inner');
    	$this->db->where('project_matrix.project_id',$id);
    	$query = $this->db->get();
    	return $query->result();	
    }
    
    /*
     * 
     * Updaters
     *  
     */
   
    function update_name($id, $new_name){
    	$this->db->where('id', $id);
    	$this->db->update('projects', array('name'=> $new_name));
    }
    
    /*
     * Add a set of matrixs to a set of projects. Set {project_id: id, matrix_id: id}
     */
    function add_matrixs($set){
    	$this->db->insert_batch('project_matrix', $set);
    }
    /*
     * Removes a set of matrixs includeds in a project. Set {project_id: id, matrix_id: id}
     */
    function remove_matrixs($set){
    	foreach($set as $inclusion){
      		$this->db->delete('project_matrix', array('id' => $inclusion['id']));
    	}
    }
    
    /*
     * 
     * Deletes
     * 
     */
    function disable($id){
    	$this->db->where('id', $id);
    	$this->db->update('projects', array('active'=> 'n'));	
    }
    
}
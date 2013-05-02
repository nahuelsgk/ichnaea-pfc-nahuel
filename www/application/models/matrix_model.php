<?php

require APPPATH.'/libraries/Mongo_db.php';

class matrix_model extends CI_Model{
	
	function __construct(){
		$this->load->database();
	}
	
	function get_all_matrixs(){
		$query = $this->db->get('matrix');
		return $query->result(); 
	}
	
	function get($id){
	 	$query = $this->db->get_where('matrix', array('id'=>$id));
	 	return $query->row_array();
	}
	
	function create($name){
		$this->load->library('Mongo_db');
		try{
	    	$mongo_id = $this->mongo_db->insert(
	    		'matrixsCollection', 
	    		array(
	    			'n_headers' => 0, 
	    			'headers' => array(), 
	    			'samples' => array()));
			$this->db->insert('matrix', array('name' => $name, 'matrix_id' => strval($mongo_id)));
			return $this->db->insert_id();
		}
		catch (Exception $e){
			print('Problems: TODO: what must be do?');
		}
	}
	
	//In the future, avoid the mysql query to get id
	function createColumn($id){
		$this->load->library('Mongo_db');
		$query = $this->db->get_where('matrix', array("id"=>$id));
		$matrix = $query->row_array();
		$matrix_id = $matrix['matrix_id'];
		$this->mongo_db ->where(array('_id' => new MongoId($matrix_id)))
						->push('headers',array(
							"variable_id" => "2" ,
							"season_id"   => "10",
							"type" 		  => "single",
							"name" 		  => "by now allias in the cmodel"))
						->update('matrixsCollection');
		$this->mongo_db ->where(array('_id' => new MongoId($matrix_id)))
						->inc(array('n_headers' => 1))
						->update('matrixsCollection');
						
	}
	
	/* 
	 * Returns a complete document of a matrix by the SQL id
	 */
	function getDocument($id){
		$this->load->library('Mongo_db');
		$query = $this->db->get_where('matrix', array("id"=>$id));
		$matrix = $query->row_array();
		$matrix_id = $matrix['matrix_id'];
		$document = $this->mongo_db->where(array('_id' => new MongoId($matrix_id)))->get('matrixsCollection');
		return $document;	
	}
	
	/* 
	 * Creates a row of the matrixs
	 * */
	function createSample($id){
	    $this->load->library('Mongo_db');
		$query = $this->db->get_where('matrix', array("id"=>$id));
		$matrix = $query->row_array();
		$matrix_id = $matrix['matrix_id'];
		
		$this->mongo_db->where(array('_id' => new MongoId($matrix_id)))
					   ->push('samples', array(
						   "origin" => "cow",
           				   "name"   => "sample 1",
            			   "date"	=> "31-12-2013",
            			   "values" => array()))
					   ->update('matrixsCollection');
	}
	
	/*
	 * Updates a value of the matrixs
	 */
	function updateValue($id, $row, $column){
		$this->load->library('Mongo_db');
		$query = $this->db->get_where('matrix', array("id"=>$id));
		$matrix = $query->row_array();
		$matrix_id = $matrix['matrix_id'];
		
		$this->mongo_db->where(array('_id' => new MongoId($matrix_id)))
						->set('samples'.$row.'values'.$column);
		print_r($this->mongo_db->updates);
						
	}
	
}
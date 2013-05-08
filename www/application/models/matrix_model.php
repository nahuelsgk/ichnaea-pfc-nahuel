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
		try{
		  $this->load->library('Mongo_db');
		  $query = $this->db->get_where('matrix', array("id"=>$id));
		  $matrix = $query->row_array();
		  $matrix_id = $matrix['matrix_id'];
		  $document = $this->mongo_db->where(array('_id' => new MongoId($matrix_id)))->get('matrixsCollection');
		  return $document;
		}
		catch (Exception $e){
		  print_r($e);
		}	
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
	function updateValue($id, $row, $column, $value){
		$this->load->library('Mongo_db');
		$query = $this->db->get_where('matrix', array("id"=>$id));
		$matrix = $query->row_array();
		$matrix_id = $matrix['matrix_id'];
		
		$this->mongo_db->where(array('_id' => new MongoId($matrix_id)))
						->set(array('samples.'.$row.'.values.'.$column => $value));
		$this->mongo_db->update('matrixsCollection');						
	}
	
	/*
	 * Returns the header definition if exists. Otherwise returns a empty array
	 */
	function getHeader($id, $header_id){
	    $document_id = $this->_getDocumentId($id);
	    $this->load->library('Mongo_db');
	    $document = $this->mongo_db->where(array('_id' => new MongoId($document_id)))->get('matrixsCollection');
	    //print_r($document[0]["headers"][$header_id]);
	    if (!empty($document[0]["headers"][$header_id])) return $document[0]["headers"][$header_id];
	    else return array();
	}
	
	/*
	 * Private function to get the document id. In the future, for a better performance
	 */
	function _getDocumentId($id){
		$query = $this->db->get_where('matrix', array("id"=>$id));
		$matrix = $query->row_array();
		return $matrix['matrix_id'];
	}
	
}
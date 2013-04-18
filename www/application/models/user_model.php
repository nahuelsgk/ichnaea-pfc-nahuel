<?php 
class user_model extends CI_Model{
  
	function __construct()
    {
        $this->load->database();
    }
    
    function login($email, $password){
    	$this->db->select('id, login, passwd');
    	$this->db->from('users');
    	$this->db->where('login', $email);
    	$this->db->where('passwd', $this->encrypt_user_password($password));
    	
    	$query = $this->db->get();
    	if($query->num_rows() == 1){
			return $query->result();
		}
		else
		{
			return false;
		}
    }
    
    function exists($email){
		$query = $this->db->get_where('users', array('login' => $email));
		if ($query->num_rows() == 1) return true;
		return false;
    }
    
    function create($email, $name, $passwd){
    	$this->db->insert('users', array('name' => $name, 'login' => $email, 'passwd' => $this->encrypt_user_password($passwd)));
    }
    
    function encrypt_user_password($string){
    	return md5($string."ipfc"); 
    }
}
?>
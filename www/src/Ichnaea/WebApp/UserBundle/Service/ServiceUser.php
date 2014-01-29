<?php 

namespace Ichnaea\WebApp\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Ichnaea\WebApp\UserBundle\Entity\User;
use Ichnaea\WebApp\UserBundle\Service\PassIchnaeaInterface;

class ServiceUser{
	
	protected $em;
	
	public function __construct(EntityManager $em){
		$this->em = $em;
	}
	
	public function create($name, $login, $password){
		
		//Check if it is already a user
		$user = $this->em->getRepository('UserBundle:User')->findByLogin($login);
		if($user) throw new \Exception('User already in the system', 255);
		
		//Create object user
       	$user = new User();
		$user->setName($name);
		$user->setLogin($login);
		$encoder  = new PassIchnaeaInterface();
       	$password = $encoder->encodePassword($password);		
		$user->setPasswd($password);
				
		//@TODO: Check if exists
		//@TODO: If exists throw a exception
		
		//Save user
		$this->em->persist($user);
		$this->em->flush();
		
		//@Send email.		
	}
	
	/*Just return the entity of the groups for the controllers*/
	public function getGroupUsers() {
		$group = $this->em->getRepository('UserBundle:Group')->findByName('user');
		return $group;
	}
	
}
?>
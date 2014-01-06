<?php 

namespace Ichnaea\WebApp\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ichnaea\WebApp\UserBundle\Entity\Group;
use Ichnaea\WebApp\UserBundle\Entity\User;

class LoadGroupData implements FixtureInterface, ContainerAwareInterface 
{
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	/**
	 * {@inheritDoc}
	 */
	
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		//First enrol all existing users to a group
		$userManager = $this->container->get('fos_user.user_manager');
		$admin_user = $userManager->findUserBy(array('username'=>'admin'));
		
		//This group will be used later
		$group_user = '';
		if (sizeof($admin_user)) echo "Admin already exists\n";
		else {
			echo "Admin do not exists. We will create admin user and two groups";
			$user = new User();
			$user->setUsername('admin');
			$user->setPlainPassword('ichnaea');
			$user->setEmail('noemail@mail.com');
			$user->setEnabled(true);
		
			//Administrators
			$group_admins = new Group('administrator');
			$group_admins = $group_admins->setRoles(array('ROLE_SUPER_ADMIN', 'ROLE_USER'));
			$user->addGroup($group_admins);
			$manager->persist($group_admins);
			$manager->flush();
			
		
			//Users
			$group_users= new Group('user');
			$group_users->setRoles(array('ROLE_USER'));
			$group_user = $group_users;
			$manager->persist($group_users);
			$manager->flush();
			
			$manager->persist($user);
			$manager->flush();
			
		}
		
		//Enroll all user that has no group, as normal users 
		$users = $userManager->findUsers();
		foreach ($users as $user)
		{
			$groups = $user->getGroups();
			if (sizeof($groups) == 0){
				$user->addGroup($group_user);
				$userManager->updateUser($user);
			}
		}
		
	}
}
<?php 
namespace Ichnaea\WebApp\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UsersController extends Controller {
	
	public function getUsersListAction()
	{
		//@TODO: only for admin roles
		$userManager = $this->get('fos_user.user_manager');
		$users = $userManager->findUsers();
		return $this->render(
				'UserBundle::list.html.twig', 
				array ('users' => $users));
	}
	
	public function editUserAction($user_id)
	{
		$userManager = $this->get('fos_user.user_manager');
		$user = $userManager->findUserBy(array('id' => $user_id));
		return $this->render(
			'UserBundle::edit.html.twig',
			array ('user' => $user)	
		);
	}
}
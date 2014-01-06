<?php 
namespace Ichnaea\WebApp\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UsersController extends Controller {
	
	public function getUsersListAction()
	{
		//@TODO: maybe do it by security.yml
		$this->checkPermissionsUser();
		
		$userManager = $this->get('fos_user.user_manager');
		$users = $userManager->findUsers();
		return $this->render(
				'UserBundle::list.html.twig', 
				array ('users' => $users));
	}
	
	public function editUserAction($user_id)
	{
		//@TODO: maybe do it by security.yml
		$this->checkPermissionsUser();
		
		$groupManager = $this->get('fos_user.group_manager');
		$sys_groups   = $groupManager->findGroups();
		$userManager  = $this->get('fos_user.user_manager');
		$user         = $userManager->findUserBy(array('id' => $user_id));
		
		$request = $this->getRequest();
		if ($request->getMethod() == 'POST'){
			$group = $request->get('group');
			//@TODO: maybe do it in a service
			$user_groups = $user->getGroups();
			$group_to_enrol  = $groupManager->findGroupByName($group);
			$user->removeGroup($user_groups[0]);
			$user->addGroup($group_to_enrol);
			$userManager->updateUser($user);
			return $this->redirect($this->generateUrl('edit_user', array ('user_id' => $user->getId())));
			
		}
		
		
		return $this->render(
			'UserBundle::edit.html.twig',
			array ('user' => $user, 'sys_groups' => $sys_groups)	
		);
	}
	
	private function checkPermissionsUser(){
		//This page is only for super_admins
		$user = $this->get('security.context')->getToken()->getUser();
		if (!in_array("ROLE_SUPER_ADMIN", $user->getRoles()))
			throw new AccessDeniedHttpException();
		
	}
}
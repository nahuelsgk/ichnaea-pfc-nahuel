<?php

namespace Ichnaea\WebApp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller{
	
	public function getDashboardAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$trainingService = $this->get('ichnaea.training_service');
		$trainings = $trainingService->getPendingOrErrorTrainingsByUser($user->getId());
		return $this->render('UserBundle::dashboard.html.twig',array('trainings'=>$trainings));	
	}
	
}

?>
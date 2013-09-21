<?php

namespace Ichnaea\WebApp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller{
	
	public function getDashboardAction()
	{
		return $this->render('UserBundle::dashboard.html.twig');	
	}
	
}

?>
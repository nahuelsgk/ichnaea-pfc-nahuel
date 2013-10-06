<?php

namespace Ichnaea\WebApp\TrainingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('IchnaeaWebAppTrainingBundle:Default:index.html.twig', array('name' => $name));
    }
}

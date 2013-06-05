<?php

namespace Ichnaea\WebApp\MatrixBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ichnaea\WebApp\MatrixBundle\Entity\Matrix;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$matrixs = $this->getDoctrine()->getRepository('MatrixBundle:Matrix')->findAll();
        return $this->render('MatrixBundle:Default:index.html.twig', array("matrixs" => $matrixs));
    }
    
    public function viewAction(){
    	
    }
    
    public function viewCreateForm(){
    	return $this->render('MatrixBundle:Default:new.html.twig');
    }
       
}

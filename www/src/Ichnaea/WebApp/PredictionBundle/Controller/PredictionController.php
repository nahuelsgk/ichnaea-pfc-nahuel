<?php

namespace Ichnaea\WebApp\PredictionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PredictionController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('IchnaeaWebAppPredictionBundle:Default:index.html.twig', array('name' => $name));
    }

 	public function predictionFormAction($matrix_id, $training_id)
	{
		$trainingService = $this->get('ichnaea.trainingService');
		$training = $trainingService->getTraining($training_id);
		return $this->render('IchnaeaWebAppPredictionBundle::form.html.twig', 
			array(
					'matrix_id'   => $matrix_id,
					'training_id' => $training_id,
		));
	}
}
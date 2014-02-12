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
		$request = $this->getRequest();
		if ($request->getMethod() == 'POST'){
			$name       = $request->request->get("name");
			$csvContent = $request->request->get("content");
			$user       = $this->getUser();
			$owner_id   = $user->getId();
			
			$predictionService = $this->get('ichnaea_web_app_prediction.service');
			$predictionMatrix = $predictionService->createMatrixPredictionFromCSV($training_id, $name, $csvContent,$owner_id);
			return $this->redirect($this->generateUrl('view_matrix_prediction', array('matrix_id' => $matrix_id, 'training_id' => $training_id, 'prediction_id' => $predictionMatrix)));	
		}
		
		return $this->render('IchnaeaWebAppPredictionBundle::form.html.twig', 
			array(
					'matrix_id'   => $matrix_id,
					'training_id' => $training_id,
					'columns'	  => $training->getColumnsSelected(),
		));
	}

	public function predictionUpdateFormAction($matrix_id, $training_id, $prediction_id)
	{
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		$predictionMatrix = $predictionService->getPredictionMatrix($matrix_id, $training_id, $prediction_id);
		$request = $this->getRequest();
		if ($request->getMethod() == 'POST'){
			$name       = $request->request->get("name");
			$csvContent = $request->request->get("content");
			$user       = $this->getUser();
			$owner_id   = $user->getId();
				
			$predictionMatrix = $predictionService->createMatrixPredictionFromCSV($training_id, $name, $csvContent,$owner_id, $prediction_id);
			return $this->redirect($this->generateUrl('view_matrix_prediction', array('matrix_id' => $matrix_id, 'training_id' => $training_id, 'prediction_id' => $predictionMatrix)));
		}
	
		return $this->render('IchnaeaWebAppPredictionBundle::form.html.twig',
				array(
						'matrix_id'     => $matrix_id,
						'training_id'   => $training_id,
						'prediction_id' => $prediction_id,
						'columns'	    => $predictionMatrix->getTraining()->getColumnsSelected(),
						'name'		    => $predictionMatrix->getName(),
						'update'	    => true
				));
	}
	
	public function predictionMatrixFormAction($matrix_id, $training_id, $prediction_id){
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		$matrixPrediction = $predictionService->getPredictionMatrix($matrix_id, $training_id, $prediction_id);
		return $this->render('IchnaeaWebAppPredictionBundle::view.html.twig',
			array(
				'matrix' => $matrixPrediction
			)
		);
	}
	
}
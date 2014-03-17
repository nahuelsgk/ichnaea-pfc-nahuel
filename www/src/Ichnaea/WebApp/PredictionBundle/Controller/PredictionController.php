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
			return $this->redirect($this->generateUrl('view_matrix_prediction', array(
					'matrix_id' => $matrix_id, 
					'training_id' => $training_id, 
					'prediction_id' => $predictionMatrix)
			)
			);	
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
	
	public function predictionMatrixViewAction($matrix_id, $training_id, $prediction_id){
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		$matrixPrediction = $predictionService->getPredictionMatrix($matrix_id, $training_id, $prediction_id);
		return $this->render('IchnaeaWebAppPredictionBundle::view.html.twig',
			array(
				'matrix_name'        => $matrixPrediction->getName(),
				'samples'	         => $matrixPrediction->getRows(),
				'columns'	         => $matrixPrediction->getTraining()->getMatrix()->getColumns(),
				'matrix_id'          => $matrixPrediction->getId(),
				'matrix_status'	     => $matrixPrediction->getStatus(),
				'training_id'	     => $matrixPrediction->getTraining()->getId(),
				'matrix_trained_id'  => $matrixPrediction->getTraining()->getMatrix()->getId(),
				#'matrix' => $matrixPrediction
			)
		);
	}
	
	public function downloadMatrixTemplateAction($matrix_id, $training_id)
	{
        $predictionService = $this->get('ichnaea_web_app_prediction.service');
        $predictionService->downloadMatrixTemplate($matrix_id);
        
        $response = new Response();
        $response->setContent($file_content);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="template.csv"');
        return $response;
        
	}
	
	public function getUserPredictionsAction(){
		$user = $this->getUser();
		$owner_id = $user->getId();
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		$predictions = $predictionService->getPredictionsByUser($owner_id);
		return $this->render(
				'IchnaeaWebAppPredictionBundle::list.html.twig',
				array(
					'predictions' => $predictions
		)
		);
	}
	
	public function sendPredictionAction($matrix_id, $training_id, $prediction_id)
	{
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		$prediction = $predictionService->sendPrediction($matrix_id, $training_id, $prediction_id);
		return $this->redirect(
				$this->generateUrl('view_matrix_prediction', 
					array(
							'matrix_id' => $matrix_id, 
							'training_id' => $training_id, 
							'prediction_id' => $prediction->getId()
					)
				)
			);
	}
	
}
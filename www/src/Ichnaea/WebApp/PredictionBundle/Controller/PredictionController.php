<?php

namespace Ichnaea\WebApp\PredictionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ichnaea\Amqp\Model\PredictModelsResult;

class PredictionController extends Controller
{
	/**
	 * Just a demo function
	 * 
	 * @param string $name
	 */
    public function indexAction($name)
    {
        return $this->render('IchnaeaWebAppPredictionBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * Render the form for create a prediction matrix and submits the form
     *  
     * @param int $matrix_id
     * @param int $training_id
     */
 	public function predictionFormAction($matrix_id, $training_id)
	{
		$trainingService = $this->get('ichnaea.trainingService');
		$training = $trainingService->getTraining($training_id);
		$request = $this->getRequest();
		if ($request->getMethod() == 'POST'){
			$name        = $request->request->get("name");
			$description = $request->request->get('description');
			$csvContent  = $request->request->get("content");
			$user        = $this->getUser();
			$owner_id    = $user->getId();
			
			$predictionService = $this->get('ichnaea_web_app_prediction.service');
			$predictionMatrix = $predictionService->createMatrixPredictionFromCSV($training_id, $name, $description, $csvContent,$owner_id);
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

	/**
	 * Render the form to update a prediction matrix and submits the form
	 * 
	 * @param int $matrix_id
	 * @param int $training_id
	 * @param int $prediction_id
	 */
	public function predictionUpdateFormAction($matrix_id, $training_id, $prediction_id)
	{
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		$predictionMatrix = $predictionService->getPredictionMatrix($matrix_id, $training_id, $prediction_id);
		$request = $this->getRequest();
		if ($request->getMethod() == 'POST'){
			$name        = $request->request->get("name");
			$description = $request->request->get("description"); 
			$csvContent  = $request->request->get("content");
			$user        = $this->getUser();
			$owner_id    = $user->getId();
				
			$predictionMatrix = $predictionService->createMatrixPredictionFromCSV($training_id, $name, $description, $csvContent,$owner_id, $prediction_id);
			return $this->redirect($this->generateUrl('view_matrix_prediction', 
					array('matrix_id' => $matrix_id, 
							'training_id' => $training_id, 
							'prediction_id' => $predictionMatrix)));
		}
	
		return $this->render('IchnaeaWebAppPredictionBundle::form.html.twig',
				array(
						'matrix_id'     => $matrix_id,
						'training_id'   => $training_id,
						'prediction_id' => $prediction_id,
						'columns'	    => $predictionMatrix->getTraining()->getColumnsSelected(),
						'name'		    => $predictionMatrix->getName(),
						'description' 	=> $predictionMatrix->getDescription(),
						'update'	    => true
				));
	}
	
	/**
	 * Render a page to view the matrix with useful information
	 * 
	 * @param int $matrix_id
	 * @param int $training_id
	 * @param int $prediction_id
	 */
	public function predictionMatrixViewAction($matrix_id, $training_id, $prediction_id){
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		$matrixPrediction = $predictionService->getPredictionMatrix($matrix_id, $training_id, $prediction_id);
		
		return $this->render('IchnaeaWebAppPredictionBundle::view.html.twig',
			array(
				'matrix_name'        => $matrixPrediction->getName(),
				'samples'	         => $matrixPrediction->getRows(),
				'columns'	         => $matrixPrediction->getColumns(),
				'matrix_id'          => $matrixPrediction->getId(),
				'matrix_status'	     => $matrixPrediction->getStatus(),
				'training_id'	     => $matrixPrediction->getTraining()->getId(),
				'matrix_trained_id'  => $matrixPrediction->getTraining()->getMatrix()->getId(),
				'matrix_description' => $matrixPrediction->getDescription(),
				'availableVars'		 => $matrixPrediction->getTraining()->getColumnsSelected(),
				#'matrix' => $matrixPrediction
			)
		);
	}
	
	/**
	 * Action to download a prediction matrix template in csv. Actually is not working
	 * 
	 * @param int $matrix_id
	 * @param int $training_id
	 * @return \Ichnaea\WebApp\PredictionBundle\Controller\Response
	 */
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
	
	/**
	 * Render a list of the predictions created by the user
	 * 
	 * @return \Ichnaea\WebApp\PredictionBundle\Controller\Response
	 */
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
	
	/**
	 * Renders the systems complete prediction list with a pagination parameter
	 * 
	 * @param int page - number of the pagination 
	 */
	
	public function listSystemPredictionsAction($page)
	{
		$predictions = $this->get('ichnaea_web_app_prediction.service')->getSystemPredictions($page);
		return $this->render('IchnaeaWebAppPredictionBundle::list.html.twig',
				array(
					'predictions'   => $predictions,
					'previous_page' => ($page-1) < 0 ? 0 : $page-1 ,
					'next_page' => $page+1,
				));
	}
	/**
	 * Action to send the prediction to queue
	 * 
	 * @param int $matrix_id
	 * @param int $training_id
	 * @param int $prediction_id
	 */
	public function sendPredictionAction($matrix_id, $training_id, $prediction_id)
	{
		$predictionService = $this->get('ichnaea_web_app_prediction.queue_manager.service');
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
	
	/**
	 * Renders the results of a prediction batch finished
	 * 
	 * @param int $matrix_id
	 * @param int $training_id
	 * @param int $prediction_id
	 */
	public function viewPredictionResultsAction($matrix_id, $training_id, $prediction_id)
	{
		$results    = $this->get('ichnaea_web_app_prediction.service')->getPredictionResults($matrix_id, $training_id, $prediction_id, 'asHTMLTables');
		$prediction = $this->get('ichnaea_web_app_prediction.service')->getPredictionMatrix($matrix_id, $training_id, $prediction_id);
		return $this->render(
				'IchnaeaWebAppPredictionBundle::results.html.twig', 
				array(
					'matrix_name'     => $prediction->getTraining()->getMatrix()->getName(),
					'training_name'   => $prediction->getTraining()->getName(),
					'prediction_name' => $prediction->getName(),
					'results'         => $results,
					'matrix_id'       => $matrix_id,
					'training_id'     => $training_id,
					'prediction_id'   => $prediction_id  
				));
	}

	/**
	 * Renders the delete confirmation page
	 * 
	 * @param int $matrix_id
	 * @param int $training_id
	 * @param int $prediction_id
	 * @throws AccessDeniedHttpException
	 */
	public function deleteConfirmationAction($matrix_id, $training_id, $prediction_id)
	{
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		
		//Get all necessary data: users, owner, prediction
		$prediction = $predictionService->getPredictionMatrix($matrix_id, $training_id, $prediction_id);
		$user = $this->get('security.context')->getToken()->getUser();
		$owner = $prediction->getOwner();
		
		//requeriments: owner or superadmin can do that
		if ($user != $owner) {
			if (!in_array("ROLE_SUPER_ADMIN", $user->getRoles()))
				throw new AccessDeniedHttpException();
		}
		
		return $this->render(
			'IchnaeaWebAppPredictionBundle:Prediction:Page/delete_confirmation_form.html.twig',
			array(
				'name'        => $prediction->getName(), 
				'description' => $prediction->getDescription(),	
				'matrix_id'     => $prediction->getTraining()->getMatrix()->getId(),
				'training_id'   => $prediction->getTraining()->getId(),
				'prediction_id' => $prediction->getId()
			)
		);
	}
	
	/**
	 * Performs a removing of a prediction
	 * 
	 * @param int $matrix_id
	 * @param int $training_id
	 * @param int $prediction_id
	 * @throws AccessDeniedHttpException
	 */
	public function deleteAction($matrix_id, $training_id, $prediction_id)
	{
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		
		//Get all necessary data: users, owner, prediction
		$prediction = $predictionService->getPredictionMatrix($matrix_id, $training_id, $prediction_id);
		$user = $this->get('security.context')->getToken()->getUser();
		$owner = $prediction->getOwner();
	
		//requeriments: owner or superadmin can do that
		if ($user != $owner) {
			if (!in_array("ROLE_SUPER_ADMIN", $user->getRoles()))
				throw new AccessDeniedHttpException();
		}
		$predictionService = $predictionService->deletePrediction($matrix_id, $training_id, $prediction_id, $user->getId());
		return $this->redirect($this->generateUrl('predictions_user'));
	}
	
}
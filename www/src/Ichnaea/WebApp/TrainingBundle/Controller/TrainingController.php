<?php

namespace Ichnaea\WebApp\TrainingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class TrainingController extends Controller
{
	
	/**
	 * Controller to list all trainable trainings
	 * 
	 */
	public function predictableTrainingListAction()
	{
		$trainingService       = $this->get('ichnaea.trainingService');
		$trainableTrainingList = $trainingService->getTrainableTrainingList();
		//echo '<pre>';\Doctrine\Common\Util\Debug::dump($trainableTrainingList);echo '</pre>';
		return $this->render(
				'IchnaeaWebAppTrainingBundle::list.html.twig', 
				array(
					'trainings' => $trainableTrainingList
				)
		);
	}
	
	public function listTrainableMatrixsAction()
	{
		$ichnaeaService = $this->get('ichnaea.service');
		$matrixs = $ichnaeaService->getTrainableMatrixs();
		return $this->render(
				'IchnaeaWebAppTrainingBundle::trainable_matrixs.html.twig',
				array('matrixs' => $matrixs)
		);
	}
	/**
	 * Controller for render and validations for creating a training
	 * 
	 * @param integer $matrix_id
	 * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function trainingFormAction($matrix_id)
    {
    	$matrixService = $this->get('ichnaea.service');
    	$n_columns     = $matrixService->getM($matrix_id);
    	$matrix 	   = $matrixService->getMatrix($matrix_id);
    	
    	$request = $this->getRequest();
    	if ($request->getMethod() == 'POST'){
    		$user             = $this->getUser();
    		$coach_id         = $user->getId();//who perfoms the training
    		
    		//Read form values
    		$name             = $request->request->get("name");
    		$description      = $request->request->get("description");
    		$this->get('logger')->info($description);
    		$columns_selection = $request->request->get("select_column");
    		$origin            = $request->request->get("origin_versus");
    		if ($origin == "all") $origin = NULL;
    		
    		//Call the service
    		$trainingService = $this->get('ichnaea.training_service');
    		$validation = $trainingService->createTraining(
    				$matrix_id, $coach_id, $name, $description, $columns_selection, $origin
			); 
		    		
    		if ($validation->valid() == FALSE){
    			$errors = $validation->getErrorsAsArrayOfStrings();
    			return $this->render(
    					'IchnaeaWebAppTrainingBundle::form.html.twig',
    					array(
    						"matrix_id"      => $matrix_id,
    						"n_columns"      => $n_columns,
    						"name" 		     => $name,
    						"description"    => $description,
    						"k1"		     => $k1,
    						"k2"		     => $k2,
    						"best_models"    => $best_models,
     						"min_size"       => $min_size_var_set,
    						"max_size"       => $max_size_var_set,
    						"type_of_search" => $type_of_search,
    						"errors"	     => $errors,
    						
    					)
    			);
    		} 	
  			$training_id = $validation->getTraining()->getId();
    		return $this->redirect($this->generateUrl('training_view', 
    				array('matrix_id' => $matrix_id, 'training_id' => $training_id)));
    	}
    	
        return $this->render(
        		'IchnaeaWebAppTrainingBundle::form.html.twig', 
        		array(
        				"matrix_id" => $matrix_id, 
        				"n_columns" => $n_columns,
        				"errors" => NULL,
        				"name" => NULL,
        				"description" => NULL,
        				"k1"		     => NULL,
        				"k2"		     => NULL,
        				"best_models"    => NULL,
        				"min_size"       => NULL,
        				"max_size"       => NULL,
        				"type_of_search" => NULL,
        				"origins"		 => $matrix->getOrigins(),
        				"columns"		 => $matrix->getColumns()
        	)
        );
    }
    
    /**
     * 
     * @param integer $matrix_id
     * @param integer $training_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewTrainingAction($matrix_id, $training_id)
    {
		$trainingService = $this->get('ichnaea.trainingService');
		$training = $trainingService->getTraining($training_id);
		
		$predictionService = $this->get('ichnaea_web_app_prediction.service');
		$predictions       = $predictionService->getPredictionsFromTraining($training_id);
		
		if($this->getRequest()->getMethod() == 'POST'){
			$status = $trainingService->checkTraining($training_id);
		}
		
		return $this->render('IchnaeaWebAppTrainingBundle::view.html.twig', 
			array(
				'training'   => $training,
				'predictions' => $predictions
			)
		);
    }

    /**
     * Render the delete training confirmation
     * 
     * @param integer $matrix_id
     * @param integer $training_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function deleteTrainingAction($matrix_id, $training_id)
	{
		//@TODO: only can do it the trainer or the admin
		$trainingService = $this->get('ichnaea.trainingService');
		$training = $trainingService->getTraining($training_id);
		return $this->render('IchnaeaWebAppTrainingBundle::delete_form.html.twig', array('training' => $training));
	}
	
	/**
	 * Resend a training.
	 * @TODO: maybe performs some validation. Only available when there are errors
	 * @param integer $matrix_id
	 * @param integer $training_id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function resendTrainingAction ($matrix_id, $training_id)
	{
		$trainingService = $this->get('ichnaea.trainingService');
		$training_id = $trainingService->resendTraining($training_id);
		return $this->redirect($this->generateUrl('training_view', array('matrix_id' => $matrix_id, 'training_id' => $training_id)));
	} 
	
	/**
	 * Removes a training
	 *  
	 * @param integer $matrix_id
	 * @param integer $training_id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function submitDeleteTrainingAction($matrix_id, $training_id){
		//@TODO: only can do it the trainer or the admin
		$trainingService = $this->get('ichnaea.trainingService');
		$trainingService->deleteTraining($training_id);
		return $this->redirect($this->generateUrl('user_dashboard'));
	}

	/**
	 * Test the queue service. May be have to move to another controller
	 * 
	 * @throws AccessDeniedHttpException
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function queueTestAction(){
		$user = $this->get('security.context')->getToken()->getUser();
		if (!in_array("ROLE_SUPER_ADMIN", $user->getRoles()))
			throw new AccessDeniedHttpException();
		$trainingService = $this->get('ichnaea.training_service');
		$result_queue = $trainingService->queueTest();
		return $this->render('IchnaeaWebAppTrainingBundle::queue_checklist.html.twig', 
				array("result_queue_status" => $result_queue["status"], "result_queue_message" => $result_queue["message"]));
	}

	public function downloadTrainingDataAction($matrix_id, $training_id)
	{
		$trainingService = $this->get('ichnaea.training_service');
		$file = $trainingService->getRdataContent($training_id);
		$response = new Response();
		$response->headers->set('Content-Type', "application/octet-stream");
		$response->headers->set('Content-Disposition', 'attachment; filename="rdata.zip"');
		$response->headers->set('Pragma', "no-cache");
		$response->headers->set('Expires', "0");
		$response->headers->set('Content-Transfer-Encoding', "binary");
		$response->setContent($file);
		return $response;
	}
	
	public function getMyTrainingsAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$trainingService = $this->get('ichnaea.training_service');
		return $this->render('IchnaeaWebAppTrainingBundle::my_trainings.html.twig');
	}
}

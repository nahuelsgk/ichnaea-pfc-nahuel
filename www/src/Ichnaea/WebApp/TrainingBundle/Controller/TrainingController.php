<?php

namespace Ichnaea\WebApp\TrainingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TrainingController extends Controller
{
    public function trainingFormAction($matrix_id)
    {
    	$matrixService = $this->get('ichnaea.service');
    	$n_columns     = $matrixService->getM($matrix_id);
    	
    	$request = $this->getRequest();
    	if ($request->getMethod() == 'POST'){
    		$user             = $this->getUser();
    		$coach_id         = $user->getId();//who perfoms the training
    		$name             = $request->request->get("name");
    		$description      = $request->request->get("description");
    		$this->get('logger')->info($description);
    		$k1			      = $request->request->get("k1");
    		$this->get('logger')->info($k1);
    		$k2			      = $request->request->get("k2");
    		$this->get('logger')->info($k2);
    		$best_models      = $request->request->get("best_models_percentage");
    		$this->get('logger')->info($best_models);
    		$min_size_var_set = $request->request->get("min_size_variable_set");
    		$this->get('logger')->info($best_models);
    		$max_size_var_set = $request->request->get("max_size_variable_set");
    		$type_of_search   = $request->request->get("type_of_search");
    		 
    		$trainingService = $this->get('ichnaea.training_service');
    		$validation = $trainingService->createTraining($matrix_id, $coach_id, $name, $description, $k1, $k2,
    				$best_models, $min_size_var_set,
    				$max_size_var_set, $type_of_search);
    		 

    		
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
    						"errors"	     => $errors
    					)
    			);
    		} 
    		
  			$training_id = $validation->getTraining()->getId();
    		return $this->redirect($this->generateUrl('training_view', array('matrix_id' => $matrix_id, 'training_id' => $training_id)));
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
        	)
        );
    }
    
    /*public function getTrainingAction($matrix_id)
    {
    	return $this->render(
    			'IchnaeaWebAppTrainingBundle::form.html.twig', 
    			array(
    					'matrix_id' => $matrix_id)
    	);
    }*/
    
    public function viewTrainingAction($matrix_id, $training_id)
    {
		$trainingService = $this->get('ichnaea.trainingService');
		$training = $trainingService->getTraining($training_id);
		
		if($this->getRequest()->getMethod() == 'POST'){
			$status = $trainingService->checkTraining($training_id);
		}
		
		return $this->render('IchnaeaWebAppTrainingBundle::view.html.twig', array('training' => $training));
    }

	public function deleteTrainingAction($matrix_id, $training_id)
	{
		//@TODO: only can do it the trainer or the admin
		$trainingService = $this->get('ichnaea.trainingService');
		$training = $trainingService->getTraining($training_id);
		return $this->render('IchnaeaWebAppTrainingBundle::delete_form.html.twig', array('training' => $training));
	}
	
	public function resendTrainingAction ($matrix_id, $training_id)
	{
		$trainingService = $this->get('ichnaea.trainingService');
		$training_id = $trainingService->resendTraining($training_id);
		return $this->redirect($this->generateUrl('training_view', array('matrix_id' => $matrix_id, 'training_id' => $training_id)));
	} 
	
	public function submitDeleteTrainingAction($matrix_id, $training_id){
		//@TODO: only can do it the trainer or the admin
		$trainingService = $this->get('ichnaea.trainingService');
		$trainingService->deleteTraining($training_id);
		return $this->redirect($this->generateUrl('user_dashboard'));
	}

	public function queueTestAction(){
		$user = $this->get('security.context')->getToken()->getUser();
		if (!in_array("ROLE_SUPER_ADMIN", $user->getRoles()))
			throw new AccessDeniedHttpException();
		$trainingService = $this->get('ichnaea.training_service');
		$result_queue = $trainingService->queueTest();
		return $this->render('IchnaeaWebAppTrainingBundle::queue_checklist.html.twig', 
				array("result_queue_status" => $result_queue["status"], "result_queue_message" => $result_queue["message"]));
	}
}

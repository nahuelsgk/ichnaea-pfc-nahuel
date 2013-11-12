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
    		$training_id = $trainingService->createTraining($matrix_id, $coach_id, $name, $description, $k1, $k2,
    				$best_models, $min_size_var_set,
    				$max_size_var_set, $type_of_search);
    		 
    		//While dev.... redirect into the origin
    		//return $this->redirect($this->generateUrl('create_training_form', array('matrix_id' => $matrix_id)));
    		return $this->redirect($this->generateUrl('training_view', array('matrix_id' => $matrix_id, 'training_id' => $training_id)));
    	}
    	
        return $this->render(
        		'IchnaeaWebAppTrainingBundle::form.html.twig', 
        		array(
        				"matrix_id" => $matrix_id, 
        				"n_columns" => $n_columns)
        );
    }
    
    public function getTrainingAction($matrix_id)
    {
    	return $this->render(
    			'IchnaeaWebAppTrainingBundle::form.html.twig', 
    			array(
    					'matrix_id' => $matrix_id)
    	);
    }
    
    public function viewTrainingAction($matrix_id, $training_id, $check)
    {
		$trainingService = $this->get('ichnaea.trainingService');
		$training = $trainingService->getTraining($training_id);
		if($check){
			$trainingService->checkTraining($training_id);
		}
		return $this->render('IchnaeaWebAppTrainingBundle::view.html.twig', array('training' => $training));
    }
}

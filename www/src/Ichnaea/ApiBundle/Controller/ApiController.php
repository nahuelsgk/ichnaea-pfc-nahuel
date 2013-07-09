<?php 
namespace Ichnaea\ApiBundle\Controller;

use Symfony\Component\BrowserKit\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends FosRestController{
    
    
    public function getSeasonAction($id)
    {   
    	$ichnaeaService = $this->get('ichnaea.service');
    	return $this->view($ichnaeaService->getSeasonById($id), 200);
    }
    
    public function getSeasonByPatternNameAction()
    {
    	$request = $this->getRequest();
    	$pattern = $request->request->get("pattern");
    	$ichnaeaService = $this->get('ichnaea.service');
    	return $this->view($ichnaeaService->getSeasonByPatterName($pattern), 200);
    }
 
    public function deleteSeasonSetAction($variable_id, $seasonSet_id){
       $ichnaeaService = $this->get('ichnaea.service');
       $ichnaeaService->deleteSeasonSet($seasonSet_id);
       return $this->view(null, 204);
    }
    
    public function deleteSeasonSetComponentAction($variable_id, $seasonSet_id, $component_id)
    {
    	$ichnaeaService = $this->get('ichnaea.service');
    	$ret = $ichnaeaService->deleteSeasonSetComponent($variable_id, $seasonSet_id, $component_id);
    	return $this->view($ret, 200);
    }
    
    public function getVariableSeasonSetAction($variable_id)
    {
    	$ichnaeaService = $this->get('ichnaea.service');
    	$seasonSets = $ichnaeaService->getVariableSeasonSets($variable_id);
    	return $this->view($seasonSets, 200);
    }
    
    public function updateMatrixColumnAction($matrix_id, $column_id)
    {
    	$request = $this->getRequest();
    	$new_name       = $request->get('name');
    	$new_variable   = $request->get('variable');
    	$new_seasonSet  = $request->get('season');
    	
    	$ichnaeaService = $this->get('ichnaea.service');
    	$ichnaeaService->updateMatrixVariable($matrix_id, $column_id, $new_name, $new_variable, $new_seasonSet);
    	return $this->view(null, 200); 	
    }
    
    public function updateSampleAction($matrix_id, $sample_id)
    {
    	$request    = $this->getRequest();
    	$new_name   = $request->get('name');
    	$new_date   = $request->get('date');
    	$new_origin = $request->get('origin'); 
    	$ichnaeaService = $this->get('ichnaea.service');
    	$ichnaeaService->updateSample($matrix_id, $sample_id, $new_name, $new_date, $new_origin);
    	return $this->view(null, 200);
    }
}
?>
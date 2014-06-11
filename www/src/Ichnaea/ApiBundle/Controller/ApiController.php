<?php 
namespace Ichnaea\ApiBundle\Controller;

use Symfony\Component\BrowserKit\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * 
 * @author Nahuel Velazco
 *
 */
class ApiController extends FosRestController{
    
    /**
     * 
     * @param int $id
     */
    public function getSeasonAction($id)
    {   
    	$ichnaeaService = $this->get('ichnaea.data_basic_manager');
    	return $this->view($ichnaeaService->getSeasonById($id), 200);
    }
    
    /**
     * @param string $pattern - String sent in the body
     */
    public function getSeasonByPatternNameAction()
    {
    	$request = $this->getRequest();
    	$pattern = $request->request->get("pattern");
    	$ichnaeaService = $this->get('ichnaea.data_basic_manager');
    	return $this->view($ichnaeaService->getSeasonByPatterName($pattern), 200);
    }
 
    /**
     * 
     * @param int $variable_id
     * @param int $seasonSet_id
     */
    public function deleteSeasonSetAction($variable_id, $seasonSet_id){
       $ichnaeaService = $this->get('ichnaea.data_basic_manager');
       $ichnaeaService->deleteSeasonSet($seasonSet_id);
       return $this->view(array('status' => 'ok'), 204);
    }
    
    /**
     * 
     * @param int $variable_id
     * @param int $seasonSet_id
     * @param int $component_id
     */
    public function deleteSeasonSetComponentAction($variable_id, $seasonSet_id, $component_id)
    {
    	$ichnaeaService = $this->get('ichnaea.data_basic_manager');
    	$ret = $ichnaeaService->deleteSeasonSetComponent($variable_id, $seasonSet_id, $component_id);
    	return $this->view(array('status' => 'ok'), 200);
    }
    
    /**
     * 
     * @param int $variable_id
     * @param int $seasonSet_id
     * @param int $component_id
     */
    public function deleteSeasonSetComponentCascadeAction($variable_id, $seasonSet_id, $component_id)
    {
    	$ichnaeaService = $this->get('ichnaea.data_basic_manager');
    	$ret = $ichnaeaService->deleteCompleteSeasonSetComponent($variable_id, $seasonSet_id, $component_id);
    	if ($ret == false) return $this->view(array('status' => 'error', 'msg' => "Can't delete this component because is in used or is shared" ), 200);
    	return $this->view(array('status' => 'ok'), 200);
    }
    
    /**
     * 
     * @param int $variable_id
     */
    public function getVariableSeasonSetAction($variable_id)
    {
    	$ichnaeaService = $this->get('ichnaea.data_basic_manager');
    	$seasonSets = $ichnaeaService->getVariableSeasonSets($variable_id);
    	return $this->view($seasonSets, 200);
    }
    
    /**
     * 
     * @param int $matrix_id
     * @param int $column_id
     */
    public function updateMatrixColumnAction($matrix_id, $column_id)
    {
    	$request = $this->getRequest();
    	$new_name       = $request->get('name');
    	$new_variable   = $request->get('variable');
    	$new_seasonSet  = $request->get('season');
    	
    	$ichnaeaService = $this->get('ichnaea.data_basic_manager');
    	$ichnaeaService->updateMatrixVariable($matrix_id, $column_id, $new_name, $new_variable, $new_seasonSet);
    	return $this->view(null, 200); 	
    }
    
    /**
     * 
     * @param int $matrix_id
     * @param int $sample_id
     */
    public function updateSampleAction($matrix_id, $sample_id)
    {
    	$request    = $this->getRequest();
    	$new_name   = $request->get('name');
    	$new_date   = $request->get('date');
    	$new_origin = $request->get('origin'); 
    	$ichnaeaService = $this->get('ichnaea.data_basic_manager');
    	$ichnaeaService->updateSample($matrix_id, $sample_id, $new_name, $new_date, $new_origin);
    	return $this->view(null, 200);
    }
    
    /**
     * 
     * @param int $matrix_id
     * @param int $sample_id
     */
    public function updateSamplePredictionAction($prediction_id, $sample_id)
    {
        $request    = $this->getRequest();
        $new_name   = $request->get('name');
        $new_date   = $request->get('date');
        $new_origin = $request->get('origin');
        $predictionService = $this->get('ichnaea_web_app_prediction.service');
        $predictionService->updateSample($prediction_id, $sample_id, $new_name, $new_date, $new_origin);
        return $this->view(null, 200);
        	
    }
    
    /**
     * 
     * @param int $matrix_id
     * @param int $sample_id
     * @param int $index
     */
    public function updateSampleDataAction($matrix_id, $sample_id, $index)
    {
    	$ichnaeaService = $this->get('ichnaea.data_basic_manager');
    	$new_data   = $this->getRequest()->get('data');
    	$ichnaeaService->updateSampleData($matrix_id, $sample_id, $index, $new_data);
    	return $this->view(null, 200);
    }
    
    /**
     *
     * @param int $matrix_id
     * @param int $sample_id
     * @param int $index
     */
    public function updateSamplePredictionDataAction($prediction_id, $sample_id, $index)
    {
    	$predictionService = $this->get('ichnaea_web_app_prediction.service');
    	$new_data   = $this->getRequest()->get('data');
    	$predictionService->updateSamplePredictionData($prediction_id, $sample_id, $index, $new_data);
    	return $this->view(null, 200);
    }
    
    public function updateColumnPredictionAction($prediction_id, $column_index)
    {
    	$predictionService = $this->get('ichnaea_web_app_prediction.service');
    	$request      = $this->getRequest();
    	$new_name     = $request->get('name');
    	$new_variable = $request->get('variable');
    	$predictionService->updateColumnPrediction($prediction_id, $column_index, $new_name, $new_variable);
    	return $this->view(null, 200);
    }
}
?>

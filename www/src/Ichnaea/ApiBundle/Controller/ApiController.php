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
    	//$view = $this->view($ichnaeaService->getSeasonById($id), 200);
    	//return $this->handleView($view);
    	return $this->view($ichnaeaService->getSeasonById($id), 200);
    }
    
    /**
     * @TODO Documentation and fix controller name
     */
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
    }
    
    public function deleteSeasonSetComponentAction($variable_id, $seasonSet_id, $component_id)
    {
    	$ichnaeaService = $this->get('ichnaea.service');
    	$ichnaeaService->deleteSeasonSetComponent($component_id);
    	return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
}
?>
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
}
?>
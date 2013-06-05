<?php 
namespace Ichnaea\ApiBundle\Controller;

use Symfony\Component\BrowserKit\Response;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends Controller{
    
    /**
     * @TODO Documentation and fix controller name
     */
    public function getAction($id)
    {   
    	$ichnaeaService = $this->get('ichnaea.service');
    	return $ichnaeaService->getSeasonById($id);
    }
    
    /**
     * @TODO Documentation and fix controller name
     */
    public function postAction()
    {
    	$request = $this->getRequest();
    	$pattern = $request->request->get("pattern");
    	$ichnaeaService = $this->get('ichnaea.service');
    	return $ichnaeaService->getSeasonByPatterName($pattern);
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
<?php
namespace Ichnaea\WebApp\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MatrixController extends Controller
{	
	public function getMatrixFormAction()
	{
		return $this->render('MatrixBundle:Matrix:form.html.twig');
	}
		
	public function createMatrixAction(){
		$request = $this->getRequest();
		$name = $request->request->get("name");
		$csvContent = $request->request->get("content");
		
		$user = $this->getUser();
		$owner_id = $user->getId();
		
		$ichnaeaService = $this->get('ichnaea.service');
		$matrix_id = $ichnaeaService->createMatrixFromCSVContent($name, $csvContent, $owner_id);
		return $this->redirect($this->generateUrl('matrix_ui', array('matrix_id'=>$matrix_id)));
	}
	
	public function listSystemsMatrixAction(){
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		$listMatrixs = $ichnaeaService->getAllMatrixs();
		return $this->render('MatrixBundle:Matrix:list.html.twig', array('matrixs' => $listMatrixs));
	}
	
	public function guiMatrixAction($matrix_id){
		$ichnaeaService = $this->get('ichnaea.service');
		$matrix = $ichnaeaService->getMatrix($matrix_id);
		$availableVars = $ichnaeaService->getAllVariables();
		//echo '<pre>';\Doctrine\Common\Util\Debug::dump($matrix);echo '</pre>';
		return $this->render('MatrixBundle:Matrix:matrix.html.twig', array('matrix' => $matrix, 'availableVars' => $availableVars));
	}
}
?>
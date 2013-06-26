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
		$ichnaeaService = $this->get('ichnaea.service');
		$ichnaeaService = $ichnaeaService->createMatrixFromCSVContent($name, $csvContent);
		return $this->render('MatrixBundle:Matrix:form.html.twig');
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
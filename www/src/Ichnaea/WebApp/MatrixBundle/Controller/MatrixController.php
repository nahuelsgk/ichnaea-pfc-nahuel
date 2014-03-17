<?php
namespace Ichnaea\WebApp\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaService;

class MatrixController extends Controller
{	
	public function getMatrixFormAction()
	{
		return $this->render('MatrixBundle:Matrix:form.html.twig', array(
				'update'    => 'n',
		));
	}
		
	public function createMatrixAction()
	{
		$request = $this->getRequest();
		$name = $request->request->get("name");
		$csvContent = $request->request->get("content");
		
		$user = $this->getUser();
		$owner_id = $user->getId(); 
		
		$ichnaeaService = $this->get('ichnaea.service');
		$matrix_id = $ichnaeaService->createMatrixFromCSVContent($name, $csvContent, $owner_id);
		return $this->redirect($this->generateUrl('matrix_ui_edit', array('matrix_id'=>$matrix_id)));
	}
	
	public function updateDataSetFormAction($matrix_id)
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		
		if ($request->getMethod() == 'POST'){
			$csvContent = $request->request->get("content");
			$name = $request->request->get("name");
			$matrix_id = $ichnaeaService->updateMatrixFromCSVContent($matrix_id, $name, $csvContent);
			return $this->redirect(
					$this->generateUrl('matrix_ui_edit', 
							array(
									'matrix_id'=>$matrix_id)
							)
					);
		}
		$matrix = $ichnaeaService->getMatrix($matrix_id);
		return $this->render('MatrixBundle:Matrix:form.html.twig', 
				array(
						'update'    => 'y',
						'matrix_id' => $matrix->getId(),
						'name'      => $matrix->getName()   
				)
		);
	}
	
	public function listSystemsMatrixAction(){
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		$listMatrixs = $ichnaeaService->getAllMatrixs();
		return $this->render('MatrixBundle:Matrix:list.html.twig', 
				array(
					'matrixs' => $listMatrixs
				)
		);
	}
	
	public function guiMatrixAction($matrix_id){
		$ichnaeaService = $this->get('ichnaea.service');
		
		$matrix        = $ichnaeaService->getMatrix($matrix_id);
		
		if ($this->getUser() != $matrix->getOwner()) {
			throw new HttpException(403, 'You are not the owner of this matrix.');
		}
		
		$user_id = $this->getUser()->getId();
		
		$request = $this->getRequest();
		if ($request->getMethod() == 'POST'){
			$visible = $request->get('visible');
			$matrix = $ichnaeaService->updateMatrixConfiguration(
					$user_id, $matrix_id, $visible == 'yes' ? TRUE : FALSE );
			
			return $this->redirect($this->generateUrl('matrix_ui_edit', 
					array('matrix_id' => $matrix->getId())));;
	   	}
	   	
		$availableVars = $ichnaeaService->getAllVariables();
		$visible       = $matrix->getVisible();
		$isTrained     = $ichnaeaService->matrixIsTrained($matrix_id);
		$complete      = $matrix->isComplete();
		return $this->render(
				'MatrixBundle:Matrix:matrix.html.twig', 
				array(
						'visible'   => $visible,
						'is_trained'=> $isTrained,
						'complete'  => $complete,
						'matrix'    => $matrix, 
						'availableVars' => $availableVars
				)
		);
	}
	
	public function saveConfigurationAction($matrix_id) {
		$ichnaeaService = $this->get('ichnaea.service');
		
	    $matrix = $ichnaeaService->getMatrix($matrix_id);
	    
	    $matrix        = $ichnaeaService->getMatrix($matrix_id);
		if ($this->getUser() != $matrix->getOwner()) {
			throw new HttpException(403, 'Unauthorized access.');
		}
		
		$request = $this->getRequest(); 
		$user_id = $this->getUser()->getId();
		$visible = $request->request->get('visible');
		$matrix = $ichnaeaService->updateMatrixConfiguration($user_id, $matrix_id, $visible == 'yes' ? TRUE : FALSE );

		return $this->resolveMatrixGUI($matrix);
	}
	
	public function viewMatrixAction($matrix_id){
		$ichnaeaService = $this->get('ichnaea.service');
		$matrix        = $ichnaeaService->getMatrix($matrix_id);
		return $this->render(
				'MatrixBundle:Matrix:view.html.twig', 
				array(
						'matrix_name' => $matrix->getName(),
						'samples'	  => $matrix->getRows(),
						'columns'	  => $matrix->getColumns(),
						'matrix_id'   => $matrix->getId(),
						#'matrix' => $matrix
				)	
		);
	}
	
	public function buildFilesAction($matrix_id)
	{
		$ichnaeaService = $this->get('ichnaea.service');
		$matrix = $ichnaeaService->buildFiles($matrix_id);
		return $this->redirect($this->generateUrl('matrix_ui_edit',array("matrix_id" => $matrix_id)));
	}
	
	public function downloadFormAction($matrix_id)
	{
		$ichnaeaService = $this->get('ichnaea.service');
		$matrix = $ichnaeaService->getMatrix($matrix_id);
	    return $this->render('MatrixBundle:Matrix:download_form.html.twig', 
	    		array(
	    				'matrix_id'   => $matrix->getId(),
	    				'matrix_name' => $matrix->getName(),
	    ));	
	}
	
	public function downloadAction($matrix_id) 
	{
		$request = $this->getRequest();
		$type    = $request->request->get("type_download");
		
		
		$ichnaeaService = $this->get('ichnaea.service');
		$file_content = $ichnaeaService->getMatrixAs('csv', $type, $matrix_id);
		
	    $response = new Response();
	    $response->setContent($file_content);
	    $response->headers->set('Content-Type', 'text/csv');
	    $response->headers->set('Content-Disposition', 'attachment; filename="matrix.csv"');
	    return $response;
	}
	
	public function cloneFormAction($matrix_id)
	{
		$ichnaeaService = $this->get("ichnaea.service");
		$matrix = $ichnaeaService->getMatrix($matrix_id);
		$today = date_create();
		
		return $this->render('MatrixBundle:Matrix:clone_form.html.twig',
	    	array(
			'matrix_name'     => $matrix->getName(),
	        'matrix_id'       => $matrix->getId(),
	        'matrix_new_name' => 'clone of '.$matrix->getName().' - '.date_timestamp_get($today)
		));
	}
	
	public function performCloneAction($matrix_id)
	{
	    
	    $request = $this->getRequest();
	    $owner   = $this->getUser();
	    $name   = $request->request->get('name');
	    
	    $ichnaeaService = $this->get('ichnaea.service');
	    $matrix        = $ichnaeaService->cloneMatrix($matrix_id, $owner ,$name);
	    $matrix_id     = $matrix->getId();
	    
	    return $this->redirect($this->generateUrl('matrix_ui_edit',array("matrix_id" => $matrix_id)));
	}
	
	private function resolveMatrixGUI($matrix){
		if($matrix->getVisible() == TRUE)
				return $this->redirect($this->generateUrl('matrix_ui_edit', array('matrix_id' => $matrix->getId())));
		
		return $this->redirect($this->generateUrl('matrix_ui_view', array('matrix_id' => $matrix->getId())));
	}
	
	public function downloadDataSetAction($matrix_id)
	{
		$ichnaeaService = $this->get('ichnaea.service');
		$file_content = $ichnaeaService->getMatrixAs('csv', 'simple', $matrix_id);
		
		$response = new Response();
		$response->setContent($file_content);
	    $response->headers->set('Content-Type', 'text/csv');
	    $response->headers->set('Content-Disposition', 'attachment; filename="matrix.csv"');
	    return $response;
	}
	
	public function listTrainableMatrixAction()
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		$listMatrixs = $ichnaeaService->getTrainableMatrixs();
		return $this->render('MatrixBundle:Matrix:list.html.twig',
				array(
						'trainable' => 'y',  
						'matrixs'   => $listMatrixs
				)
		);
	}
	
	public function getMyMatrixsAction()
	{
		return $this->render('MatrixBundle:Matrix:my_matrix.html.twig');
	}
}
?>
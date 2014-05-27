<?php 
namespace Ichnaea\WebApp\MatrixBundle\Controller;

use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaService;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class VariableController extends Controller{
	
	public function getVariablesListAction()
	{
		$ichnaeaService = $this->get('ichnaea.service');
		$variables = $ichnaeaService->getAllVariables();
		return $this->render('MatrixBundle:Variable:list.html.twig', array("variables" => $variables));
	}
	
	public function getFormVariableAction($variable_id = NULL)
	{
        $variable = NULL;
        $action = 'create';
        $season_sets = NULL;
		if (!is_null($variable_id)) {	
			$action = 'update';
			$ichnaeaService = $this->get('ichnaea.service');
			$variable = $ichnaeaService->getVariableById($variable_id);
			
		}
		return $this->render(
		    'MatrixBundle:Variable:form.html.twig', 
		    array(
		         "variable"    => $variable, 
		         "action"      => $action, 
		    
		    )
		);
	}
	
	public function createVariableAction()
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		$name  = $request->request->get('var_name');
		//@TODO Check error name 
		$notes = $request->request->get('var_description');
		$ichnaeaService->createVariable($name, $notes);
		return $this->redirect($this->generateUrl('variable_list'));
	}
	
	public function updateVariableAction($variable_id)
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		$name  = $request->request->get('var_name');
		//@TODO Check error name 
		$notes = $request->request->get('var_description');
		$variable[0] = $ichnaeaService->updateVariable($variable_id, $name, $notes);
		return $this->redirect($this->generateUrl('variable_edit', array('variable_id'=>$variable_id)));	
	}
	
	
	/**
	 * Util function to build an array to help controller to pass Dataset to the services
	 * 
	 * @param unknown $request
	 * @return multitype:
	 */
	private function buildSeasonComponents($request){
		$seasons = array();
		$n_files = 0;
		if($request->get('file_0') != '' && $request->get('content_0') != ''){
			$seasons[$n_files]['filename'] = $request->get('file_0');
		  	$seasons[$n_files]['type']      = $request->get('season_0');
		  	$seasons[$n_files]['content']   = $request->get('content_0');
		  	$n_files++;
		}
		if($request->get('file_1') != '' && $request->get('content_1') != ''){
			$seasons[$n_files]['filename'] = $request->get('file_1');
			$seasons[$n_files]['type']      = $request->get('season_1');
			$seasons[$n_files]['content']   = $request->get('content_1');
		}
		return $seasons;
	}
	
	/**
	 * 
	 * @param unknown $variable_id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function seasonSetFormAction($variable_id)
	{	
		$request = $this->get('request');
		
		if ($request->getMethod() == 'POST') {
			$request = $this->getRequest();
			$ichnaeaService = $this->get('ichnaea.service');
			$name = $request->get('season_set_name');
			
			//read season file contents
			$seasons = $this->buildSeasonComponents($request);

			//Season that are already in the system and were choosen
			//We build a mapping association [seasonId] => summer|winter|spring....
			$already_season = array();
			$season_id = $request->get('season_id');
			//Read on the form why is season_3
			$already_season[$season_id] = $request->get('season_3');
		
			$season_id = $ichnaeaService->createSeasonSet(
					$variable_id,
				    $name, 
					$already_season,
					$seasons
			);
			return $this->redirect($this->generateUrl('season_set_edit', array('variable_id'=>$variable_id, 'season_set_id' => $season_id )));
		}

		//... by default is get request
		$action = 'create';
		$season_set = NULL;
		$season_set_components = NULL;
		$ichnaeaService = $this->get('ichnaea.service');
		$season = $ichnaeaService->getVariableById($variable_id);
		
		return $this->render(
			'MatrixBundle:Variable:SeasonSet/form.html.twig', 
			array(
				'variable_id' 			=> $variable_id,
				'action'      			=> $action,
				'variable_name'			=> $season->getName(),
				'season_set'  			=> $season_set,
				'season_set_components' => $season_set_components
			)
		);
	}
	
	/**
	 * Controls the render and the request for delete a season Set
	 * @param unknown $variable_id
	 * @param unknown $season_set_id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function seasonSetCompleteDestroyFormAction($variable_id, $season_set_id)
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		
		if($request->getMethod() == 'POST'){
			//perform season massacre
			$return = $ichnaeaService->deleteSeasonSetCascade($season_set_id);
			
			if (!$return){
				return $this->render('::error.html.twig', 
					array(
						'message'      => 'Could not be deleted beacauthis season set is in used by a matrix',
						'continue_url' => $this->generateUrl('variable_edit', array('variable_id' => $variable_id)),
				));
			}
			
			//redirect into the variable form
			return $this->redirect($this->generateUrl('variable_edit', array('variable_id' => $variable_id)));
		}
		
		
		$seasonSet = $ichnaeaService->getSeasonSet($season_set_id);
		return $this->render('MatrixBundle:Variable:SeasonSet/confirmation.html.twig', array('season_set'=>$seasonSet));
	}
	
	public function editSeasonSetFormAction($variable_id, $season_set_id)
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		
		if ($request->getMethod() == 'POST'){
		  $name = $request->get("season_set_name");
		  $new_seasons = $request->get("season_id");
		  
		  $seasons = $this->buildSeasonComponents($request);
		  
		  //Season that are already in the system and were choosen
		  //We build a mapping association [seasonId] => summer|winter|spring....
		  $already_season = array();
		  $season_id = $request->get('season_id');
		  //Read on the form why is season_3
		  $already_season[$season_id] = $request->get('season_3');
		  
		  $ichnaeaService->updateSeasonSet($season_set_id, $name, $already_season, $seasons);
		  return $this->redirect($this->generateUrl('season_set_edit', array('variable_id' => $variable_id, 'season_set_id' => $season_set_id)));
		}
		
		$action = 'update';
		$season_set = $ichnaeaService->getSeasonSet($season_set_id);
		$season_set_components = $season_set->getComponents();
		
		return $this->render(
				'MatrixBundle:Variable:SeasonSet/form.html.twig',
				array(
						'variable_id' 			=> $variable_id,
						'action'      			=> $action,
						'variable_name'			=> $season_set->getVariable()->getName(),
						'season_set'  			=> $season_set,
						'season_set_components' => $season_set_components
		));
	}
}

?>

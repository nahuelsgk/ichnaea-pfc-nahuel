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
	
	public function seasonSetFormAction($variable_id, $season_set_id = NULL)
	{	
		$action = 'create';
		$season_set = NULL;
		$season_set_components = NULL;
		$ichnaeaService = $this->get('ichnaea.service');
		$season = $ichnaeaService->getVariableById($variable_id);
		if (!is_null($season_set_id)) {	
			$action = 'update';
			
			$season_set = $ichnaeaService->getSeasonSet($season_set_id);
			$season_set_components = $season_set->getSeason();
		}
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
	
	public function createSeasonSetAction($variable_id)
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		$name = $request->get("season_set_name");
			
		$already_seasons = $request->get("season_id");
		$season_id = $ichnaeaService->createSeasonSet($variable_id, $name, array_filter($already_seasons));
		return $this->redirect($this->generateUrl('season_set_edit', array('variable_id'=>$variable_id, 'season_set_id' => $season_id )));
	}
	
	public function updateSeasonSetAction($variable_id, $season_set_id = NULL)
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.service');
		$name = $request->get("season_set_name");
		$new_seasons = $request->get("season_id");
		$ichnaeaService->updateSeasonSet($season_set_id, $name, array_filter($new_seasons));
		return $this->redirect($this->generateUrl('season_set_edit', array('variable_id' => $variable_id, 'season_set_id' => $season_set_id)));
	}
}

?>

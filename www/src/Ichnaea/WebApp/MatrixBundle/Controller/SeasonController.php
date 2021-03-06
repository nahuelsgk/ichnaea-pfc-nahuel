<?php 

namespace Ichnaea\WebApp\MatrixBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SeasonController extends Controller{
	
	public function indexAction(){
		$ichnaeaService = $this->get('ichnaea.data_basic_manager');
		$seasons = $ichnaeaService->getAllSeasons();
		return $this->render('MatrixBundle:Season:list.html.twig', array("seasons" => $seasons));	
	}
	
	//@TODO: check errors
	public function createSeasonAction()
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.data_basic_manager');
		$name       = $request->request->get("name");
		$notes      = $request->request->get("notes");
		$start_date = $request->request->get("start_date");
		$end_date   = $request->request->get("end_date");
		$content    = $request->request->get("content");
		$season     = $ichnaeaService->createSeason($name, $notes, $start_date, $end_date, $content);
		return $this->redirect($this->generateUrl('season_edit', array('season_id' => $season->getId())));
	}
	
	public function updateSeasonAction($season_id)
	{
		$request = $this->getRequest();
		$ichnaeaService = $this->get('ichnaea.data_basic_manager');
		$name       = $request->request->get("name");
		$notes      = $request->request->get("notes");
		$start_date = $request->request->get("start_date");
		$end_date   = $request->request->get("end_date");
		$content    = $request->request->get("content");
		$season_id  = $ichnaeaService->updateSeason($season_id, $name, $notes, $start_date, $end_date, $content);
		return $this->redirect($this->generateUrl('season_edit', array('season_id' => $season_id)));
	}
	
	public function seasonFormAction($season_id = NULL){
		$request = $this->getRequest();
		$season = NULL;
		if($season_id){
			$ichnaeaService = $this->get('ichnaea.data_basic_manager');
			$season = $ichnaeaService->getSeasonById($season_id);
		}
		return $this->render('MatrixBundle:Season:form.html.twig', array("season" => $season));
	}
}
?>

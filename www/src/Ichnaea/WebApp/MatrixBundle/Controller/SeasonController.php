<?php 

namespace Ichnaea\WebApp\MatrixBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SeasonController extends Controller{
	
	public function indexAction(){
		$ichnaeaService = $this->get('ichnaea.service');
		$seasons = $ichnaeaService->getAllSeasons();
		return $this->render('MatrixBundle:Season:list.html.twig', array("seasons" => $seasons));	
	}
	
	//@TODO: Separate GET from POST controller in season form
	public function formAction($season_id = NULL){
		//Create a season
		$request = $this->getRequest();
		$season = NULL;
		if($request->getMethod() == 'POST' && is_null($season_id)){
		    $ichnaeaService = $this->get('ichnaea.service');
		    $name       = $request->request->get("name");
		    $notes      = $request->request->get("notes");
		    $start_date = $request->request->get("start_date");
		    $end_date   = $request->request->get("end_date");
		    $content    = $request->request->get("content");
		    $ichnaeaService->createSeason($name, $notes, $start_date, $end_date, $content);
		    //@TODO Redirect to matrix/list with a notification succeded
		}
		//Update the season
		elseif($request->getMethod() == 'POST' && $season_id){
			$ichnaeaService = $this->get('ichnaea.service');
		    $name       = $request->request->get("name");
		    $notes      = $request->request->get("notes");
		    $start_date = $request->request->get("start_date");
		    $end_date   = $request->request->get("end_date");
		    $content    = $request->request->get("content");
		    //@TODO Fix this bad encoding
		    $season[0] = $ichnaeaService->updateSeason($season_id, $name, $notes, $start_date, $end_date, $content);
		}
		elseif($request->getMethod() == 'GET' && $season_id){
			$ichnaeaService = $this->get('ichnaea.service');
			$season = $ichnaeaService->getSeasonById($season_id);
		}
		//Render the view
		return $this->render('MatrixBundle:Season:form.html.twig', array("season" => $season));
	}
}
?>
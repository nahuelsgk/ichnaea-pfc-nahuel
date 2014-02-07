<?php 
namespace Ichnaea\WebApp\PredictionBundle\Services;

use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaService;
class PredictionService
{
	protected $em;
	protected $srvc;
	
	public function __construct(EntityManager $em, IchnaeaService $srvc){
		$this->em   = $em;
		$this->srvc = $srvc; 
	}
	
	public function createMatrixPredictionFromMatrix() {
		
	}
}

?>
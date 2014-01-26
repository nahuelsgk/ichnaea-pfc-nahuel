<?php 

namespace Ichnaea\WebApp\TrainingBundle\Model;

use Ichnaea\WebApp\TrainingBundle\Entity\Training;

class TrainingFS
{
	//Configuration path for the data
	//TODO: move to configuration file or to a service: training.filesystem
	private static $basepath = "/opt/lampp/htdocs/ichnaea/ichnaea_data"; 
	
	public function saveTrainingData($training)
	{
		$training->getData();
	}
}
?>

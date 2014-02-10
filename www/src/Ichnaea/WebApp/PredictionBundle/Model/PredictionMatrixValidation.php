<?php 
namespace Ichnaea\WebApp\PredictionBundle\Model;

use Ichnaea\WebApp\MatrixBundle\Entity\Matrix as Matrix;
use Ichnaea\WebApp\TrainingBundle\Entity\Training as Training;

class PredictionMatrixValidation
{
	
	public function __construct(Training $training)
	{
		$this->training = $training;
	}
	
}
?>
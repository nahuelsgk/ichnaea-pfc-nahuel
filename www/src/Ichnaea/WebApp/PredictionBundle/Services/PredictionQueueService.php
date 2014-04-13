<?php 
namespace Ichnaea\WebApp\PredictionBundle\Services;

require_once __DIR__.' /../../../../../../amqp/php/vendor/autoload.php';

use Ichnaea\WebApp\MatrixBundle\Service\IchnaeaService;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix as PredictionMatrix;
use Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as MatrixUtils;
use Ichnaea\Amqp\Model\BuildModelsRequest as BuildModelsRequest;
use Ichnaea\Amqp\Model\PredictModelsRequest as PredictModelsRequest;
use Ichnaea\Amqp\Model\PredictModelsResponse as PredictModelsResponse;
use Ichnaea\Amqp\Model\PredictModelsResult as PredictModelsResult;
use Ichnaea\Amqp\Connection as Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;

class PredictionQueueService
{
	protected $em;
	protected $con;

	/**
	 * Service constructor
	 *
	 * @param EntityManager $em
	 * @param String $connection_user
	 * @param String $connection_pass
	 * @param integer $connection_host
	 */
	public function __construct(EntityManager $em, $connection_user, $connection_pass, $connection_host){
		$this->em   = $em;
		$this->con  = new Connection($connection_user.':'.$connection_pass.'@'.$connection_host);
	}
	
	/**
	 * Builds a dataset and sends it to the queue. If it couldn't be sent, it will marked as pending
	 *
	 * @param unknown $matrix_id
	 * @param unknown $training_id
	 * @param unknown $prediction_id
	 * @return unknown
	 */
	public function sendPrediction($matrix_id, $training_id, $prediction_id)
	{
		$predict_matrix = $this->em->getRepository('IchnaeaWebAppPredictionBundle:PredictionMatrix')->find($prediction_id);
	
		//Just prepare the data and the cue
		$data = MatrixUtils::buildDatasetFromMatrix(
				NULL,
				'simple',
				$predict_matrix->getTraining()->getMatrix()->getColumns(),
				$predict_matrix->getRows()
		);
	
		//@TODO: read training data zip
		$training_file = "/opt/lampp/htdocs/ichnaea/ichnaea_data/trainings/".$training_id."/r_data.zip";
		$fd            = fopen($training_file, "r");
		$content       = fread($fd, filesize($training_file));
		$data['data']  = $content;
	
		//Still preparing the dataset
		$data['type']  = 'predict-models';
			
		//build the data array for the dataset
		$model         = PredictModelsRequest::fromArray($data);
		//... set the new request id for the cue...
		$predict_matrix->setRequestId($model->getId());
			
		try {
			$this->con->open();
			$this->con->send($model);
			$this->con->close();
			//set status as sent
			$predict_matrix->setStatusAsSent();
		}
		catch (\Exception $e)
		{
			//if any connection problem... set the status as pending
			$predict_matrix->setStatusAsPending();
		}
	
		//Persist the entity
		$this->em->persist($predict_matrix);
		$this->em->flush();
		return $predict_matrix;
	}
}
?>
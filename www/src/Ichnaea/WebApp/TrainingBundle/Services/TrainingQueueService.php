<?php 
namespace Ichnaea\WebApp\TrainingBundle\Services;

//@TODO: change autoload forms
require_once __DIR__.' /../../../../../../amqp/php/vendor/autoload.php';
#require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';

use Doctrine\ORM\EntityManager as SymfonyEM;
use Ichnaea\WebApp\TrainingBundle\Entity\Training;
use Ichnaea\WebApp\TrainingBundle\Model\TrainingValidation;
use Ichnaea\WebApp\MatrixBundle\Service\MatrixUtils as MatrixUtils;
use Ichnaea\Amqp\Model\BuildModelsRequest as BuildModelsRequest;
use Ichnaea\Amqp\Model\BuildModelsResponse as BuildModelsResponse;
use Ichnaea\Amqp\Connection as Connection;
use Ichnaea\WebApp\PredictionBundle\Services\PredictionService as PredictionService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
 
/**
 * 
 * @author Nahuel Velazco
 *
 */
class TrainingQueueService{
	
	protected $em;
	protected $mfs;
	protected $con;
	protected $data_path;
	
	/**
	 * Constructor service
	 * 
	 * @param SymfonyEM $em
	 * @param String $connection_user
	 * @param String $connection_pass
	 * @param String $connection_host
	 * @param int $data_path
	 */
	public function __construct($connection_user, $connection_pass, $connection_host, $data_path)
	{
		$this->con        = new Connection($connection_user.':'.$connection_pass.'@'.$connection_host); 
		$this->data_path = $data_path;
	}

	/**
	 * Send a training to a queue
	 * 
	 * @param int $training_id
	 */
	public function sendTraining($model) 
	{
	  $this->con->open();
	  $this->con->send($model);
	  $this->con->close();
	  return $training->getId();
	}
		
	/**
	 * Simple test to check if the queue is available
	 * 
	 * @return boolean
	 */
	public function queueTest(){
		$result = array('status' => true, 'message'=>null);
		try{
		  $this->con->open();
		  $this->con->close();
		}
		catch(\Exception $e)
		{
			$result['status'] = false;
			$result['message'] = $e->getMessage();
		}
		return $result;
	}
}
?>
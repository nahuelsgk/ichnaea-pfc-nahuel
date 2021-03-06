<?php
namespace Ichnaea\WebApp\PredictionBundle\Command;


#require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';
//@TODO: do it by composer
require_once __DIR__.'/../../../../../../amqp/php/vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ichnaea\Amqp\Model\PredictModelsResponse as PredictModelsResponse;
use Ichnaea\Amqp\Connection as AmqpConnection;

#define('ICHNAEA_AMQP_URL', 'test:test@localhost');

class PredictionCommand extends ContainerAwareCommand
{
			
	protected function configure()
	{
		$this
		->setName('prediction:consumer')
		->setDescription('Consumer server for predictions');
		//@TODO: still don't know if i need parameters
		//->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
		//->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
	}

	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("Starting execution of consumer for predictions...");
		$amqp = new AmqpConnection(ICHNAEA_AMQP_URL);
		$amqp->open();
		
		//$em = $this->getContainer()->get('doctrine')->getEntityManager();
		$predictionService = $this->getContainer()->get('ichnaea_web_app_prediction.service');
		
		//By now is only fired when it is finished
		$amqp->listenForPredictModelsResponse(function (PredictModelsResponse $resp) use ($predictionService){
			print "Received predict-models response ".$resp->getId()." ".intval($resp->getProgress()*100)."%\n";
			$data = $resp->toArray();
			
			$data['result'] = $resp->getResult();
			var_dump($data['result']);
			if($resp->getResult()->isFinished()) {
				echo "****Consumer: Was finished ****";
				$data['result'] = $resp->getResult();
				
			} else {
				echo "****Consumer continue ***";
				unset($data['result']);
			}
			$predictionService->updatePrediction($resp->getId(), $data['progress'], $data['error'], isset($data['result']) ? $data['result'] : 'NULL');
		});
		$amqp->wait();
	}
}
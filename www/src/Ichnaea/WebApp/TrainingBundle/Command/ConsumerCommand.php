<?php
namespace Ichnaea\WebApp\TrainingBundle\Command;

require_once __DIR__.'/../../../../../../../ichnaea.alt/amqp/php/vendor/autoload.php';

use Ichnaea\WebApp\TrainingBundle\Entity\Training as Training;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ichnaea\Amqp\Model\BuildModelsResponse as BuildModelsResponse;
use Ichnaea\Amqp\Connection as AmqpConnection;

define('ICHNAEA_AMQP_URL', 'test:test@localhost');

class ConsumerCommand extends ContainerAwareCommand
{
			
	protected function configure()
	{
		$this
		->setName('training:consumer')
		->setDescription('Consumer server');
		//@TODO: still don't know if i need parameters
		//->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
		//->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
	}
	/*
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$name = $input->getArgument('name');
		if ($name) {
			$text = 'Hello '.$name;
		} else {
			$text = 'Hello';
		}

		if ($input->getOption('yell')) {
			$text = strtoupper($text);
		}

		$output->writeln($text);
	}*/
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("Starting execution of consumer...");
		$amqp = new AmqpConnection(ICHNAEA_AMQP_URL);
		$amqp->open();
		
		$em = $this->getContainer()->get('doctrine')->getEntityManager();
		
		$amqp->listenForBuildModelResponse(function (BuildModelsResponse $resp) use ($em) {
			print "Received build-models response ".$resp->getId()." ".intval($resp->getProgress()*100)."%\n";
			
			$data = $resp->toArray();
			
			//We must update the training searching by request id
			$training = $em->getRepository('IchnaeaWebAppTrainingBundle:Training')->findOneBy(array('requestId'=>$resp->getId()));
			if ($training instanceof Training) 
			{
			    $training->setProgress($data['progress']);
			    $training->setError($data['error']);
			    $em->flush();
			}
		});
		$amqp->wait();
	}
}
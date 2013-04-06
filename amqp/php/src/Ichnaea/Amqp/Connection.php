<?php

namespace Ichnaea\Amqp;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;

use Ichnaea\Amqp\Model\BuildModelsRequest;
use Ichnaea\Amqp\Model\BuildModelsResponse;
use Ichnaea\Amqp\Xml\BuildModelsRequestWriter;
use Ichnaea\Amqp\Xml\BuildModelsResponseReader;

class Connection
{
	private $conn = null;
	private $ch = null;
	private $opts = array();
	private $url = array();

	public function __construct($url, array $options=array())
	{
		$this->setOptions($options);
		$this->setUrl($url);
	}

	private function setOptions(array $options)
	{
		$this->opts = array_merge(array(
			"build-models.request.queue"	=> "ichnaea.build-models.request",
			"build-models.request.exchange"	=> "ichnaea.build-models.request",
			"build-models.response.queue"	=> "ichnaea.build-models.response",			
		), $this->opts, $options);
	}

	private function setUrl($url)
	{
		if (strpos($url, "://") === false) {
			$url = "amqp://".$url;
		}
		$parts = array_merge(array(
			"port" => 5672,
			"path" => "",
			"host" => "localhost",
			"user" => "",
			"pass" => ""
		),parse_url($url));
		if (substr($parts['path'],0,1) !== "/") {
			$parts['path'] = "/".$parts['path'];
		}
		$this->url = $parts;		
	}

	public function open()
	{
		if ($this->ch || $this->conn) {
			throw new \UnexpectedValueException("Connection is already open");
		}
		$this->conn = new AMQPConnection($this->url['host'], $this->url['port'], $this->url['user'], $this->url['pass'], $this->url['path']);
		$this->ch = $this->conn->channel();
		$this->ch->queue_declare($this->opts['build-models.request.queue'], false, false, false, null);
		$this->ch->exchange_declare($this->opts['build-models.request.exchange'], "direct", true);
		$this->ch->queue_bind($this->opts['build-models.request.queue'], $this->opts['build-models.request.exchange'], "");
		$this->ch->queue_declare($this->opts['build-models.response.queue'], false, false, false, null);
	}

	public function sendBuildModelsRequest(BuildModelsRequest $req)
	{
		if (!$this->ch) {
			throw new \UnexpectedValueException("Connection is not open");
		}
		$xml = new BuildModelsRequestWriter();
		$xml->build($req);
		$msg = new AMQPMessage($xml, array('content_type' => 'text/xml'));
		$msg->set("reply_to", $req->getId());
		$this->ch->basic_publish($msg, $this->opts['build-models.request.exchange']);
		return $xml;
	}

	public function send($model)
	{
		if ($model instanceof BuildModelsRequest) {
			return $this->sendBuildModelsRequest($model);
		}
	}

	public function listenForBuildModelResponse(\Closure $callback)
	{
		$this->ch->basic_consume($this->opts['build-models.response.queue'], "",
			false, false, false, false, function(AMQPMessage $msg) use ($callback){
				$reader = new BuildModelsResponseReader();
				$resp = $reader->read($msg->body);
				if ($resp) {
					call_user_func($callback, $resp);
					 $msg->delivery_info['channel']->
        				basic_ack($msg->delivery_info['delivery_tag']);
				}
		});
	}

	public function close()
	{
		if (!$this->ch || !$this->conn) {
			throw new \UnexpectedValueException("Connection is not open");
		}		
		$this->ch->close();
		$this->ch = null;
		$this->conn->close();
		$this->conn = null;		
	}

	public function wait()
	{
		register_shutdown_function(array($this, 'close'));
		while (count($this->ch->callbacks)) {
    		$this->ch->wait();
		}
	}
}
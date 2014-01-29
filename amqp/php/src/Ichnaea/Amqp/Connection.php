<?php

namespace Ichnaea\Amqp;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

use Ichnaea\Amqp\Model\BuildModelsRequest;
use Ichnaea\Amqp\Xml\BuildModelsRequestWriter;
use Ichnaea\Amqp\Xml\BuildModelsResponseReader;

/**
 * The Ichnaea Amqp connection
 *
 * Current options are:
 * * `build-models.request.queue`: the name of the build models request queue
 * * `build-models.request.exchange`: the name of the build models request exchange
 * * `build-models.response.queue`: the name of the build models response queue
 *
 * Sending requests can be done in the http server, but listening for responses
 * should be done in a script, since it will block the main thread.
 *
 * A typical consumer script whould look like this:
 * ```
 * $amqp = new AmqpConnection(ICHNAEA_AMQP_URL);
 * $amqp->open();
 * $amqp->listenForBuildModelResponse(function (BuildModelsResponse $resp) use ($db) {
 *     print "Received build-models response ".$resp->getId()." ".intval($resp->getProgress()*100)."%\n";
 * });
 * $amqp->wait();
 * $amqp->close();
 * ```
 *
 * Look at the `app` directory for a working example.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class Connection
{
    /**
     * The connection to the amqp server
     *
     * @var AMQPConnection
     */
    private $conn = null;

    /**
     * The amqp server channel
     *
     * @var AMQPChannel
     */
    private $ch = null;

    /**
     * Additional options to the ichnaea connection
     *
     * @var array
     */
    private $opts = array();

    /**
     * The amqp server url in parts
     *
     * @see parse_url
     * @var url
     */
    private $url = array();

    /**
     * Constructor.
     *
     * @param mixed $url     the url to the ichnaea amqp server
     * @param array $options aditional options
     */
    public function __construct($url, array $options=array())
    {
        $this->setOptions($options);
        $this->setUrl($url);
    }

    /**
     * Sets the connection options
     *
     * @param array $options the options
     */
    private function setOptions(array $options)
    {
        $this->opts = array_merge(array(
            "build-models.request.queue"	=> "ichnaea.build-models.request",
            "build-models.request.exchange"	=> "ichnaea.build-models.request",
            "build-models.response.queue"	=> "ichnaea.build-models.response",
        ), $this->opts, $options);
    }

    /**
     * Sets the connection url
     *
     * @param mixed $url the url
     */
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

    /**
     * Opens the connection to the server
     */
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

    /**
     * Sends a build models request to the server
     *
     * @param BuildModelsRequest $req the request
     */
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

    /**
     * Sends a request to the server
     *
     * @param mixed $req the request
     */
    public function send($model)
    {
        if ($model instanceof BuildModelsRequest) {
            return $this->sendBuildModelsRequest($model);
        }
    }

    /**
     * Listens for build model responses from the server. The callback will recieve
     * the response object as a parameter
     *
     * @param \Closure $callback the callback called when a response arrives
     */
    public function listenForBuildModelResponse(\Closure $callback)
    {
        $this->ch->basic_consume($this->opts['build-models.response.queue'], "",
            false, false, false, false, function (AMQPMessage $msg) use ($callback) {
                $reader = new BuildModelsResponseReader();
                $resp = $reader->read($msg->body);
                if ($resp) {
                    call_user_func($callback, $resp);
                     $msg->delivery_info['channel']->
                        basic_ack($msg->delivery_info['delivery_tag']);
                }
        });
    }

    /**
     * Closes the connection
     */
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

    /**
     * Blocks the current thread and waits for responses.
     * Should only be used in scripts.
     */
    public function wait()
    {
        register_shutdown_function(array($this, 'close'));
        while (count($this->ch->callbacks)) {
            $this->ch->wait();
        }
    }
}
<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\FakeRequest;

/**
 * An XML writer that writes FakeRequest objects to xml
 *
 * @see Ichnaea\Amqp\Model\FakeRequest
 * @author Miguel Ibero <miguel@ibero.me>
 */
class FakeRequestWriter extends Writer
{
    /**
     * Constructor
     *
     * @param \DOMDocument the document to use
     * @param \DOMElement the element to use as root
     */
    public function __construct($document=null, $root="request")
    {
        parent::__construct($document, $root);
    }

    /**
     * Write the request data into the xml
     *
     * @param FakeRequest the request object
     */
    public function build(FakeRequest $req)
    {
        $xmlRoot = $this->getRoot();
        $xmlRoot->setAttribute("id", $req->getId());
        $xmlRoot->setAttribute("type", "fake");
        $xmlRoot->setAttribute("duration", $req->getDuration());
        $xmlRoot->setAttribute("interval", $req->getInterval());
    }
}

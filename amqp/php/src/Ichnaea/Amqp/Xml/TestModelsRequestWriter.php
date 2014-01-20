<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\TestModelsRequest;

/**
 * An XML writer that writes TestModelsRequest objects to xml
 *
 * @see Ichnaea\Amqp\Model\TestModelsRequest
 * @author Miguel Ibero <miguel@ibero.me>
 */
class TestModelsRequestWriter extends Writer
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
     * @param BuildModelsRequest the request object
     */
    public function build(TestModelsRequest $req)
    {
        $xmlRoot = $this->getRoot();
        $xmlRoot->setAttribute("id", $req->getId());
        $xmlRoot->setAttribute("type", "test_models");

        // write dataset
        if (!$req->getDataset()->isEmpty()) {
            $xmlDataset = $this->createElement("dataset");
            $xmlRoot->appendChild($xmlDataset);
            $writer = new DatasetWriter($this->getDocument(), $xmlDataset);
            $writer->build($req->getDataset());
        }
    }

}

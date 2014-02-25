<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\PredictModelsRequest;

/**
 * An XML writer that writes PredictModelsRequest objects to xml
 *
 * @see Ichnaea\Amqp\Model\PredictModelsRequest
 * @author Miguel Ibero <miguel@ibero.me>
 */
class PredictModelsRequestWriter extends Writer
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
    public function build(PredictModelsRequest $req)
    {
        $xmlRoot = $this->getRoot();
        $xmlRoot->setAttribute("id", $req->getId());
        $xmlRoot->setAttribute("type", "predict_models");

        // write dataset
        if (!$req->getDataset()->isEmpty()) {
            $xmlDataset = $this->createElement("dataset");
            $xmlRoot->appendChild($xmlDataset);
            $writer = new DatasetWriter($this->getDocument(), $xmlDataset);
            $writer->build($req->getDataset());
        }
    }

}

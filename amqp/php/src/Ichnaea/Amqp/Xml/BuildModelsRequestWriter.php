<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\BuildModelsRequest;
use Ichnaea\Amqp\Model\BuildModelsFakeRequest;

/**
 * An XML writer that writes BuildModelsRequest objects to xml
 *
 * @see Ichnaea\Amqp\Model\BuildModelsRequest
 * @author Miguel Ibero <miguel@ibero.me>
 */
class BuildModelsRequestWriter extends Writer
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
    public function build(BuildModelsRequest $req)
    {
        $xmlRoot = $this->getRoot();
        $xmlRoot->setAttribute("id", $req->getId());
        $xmlRoot->setAttribute("type", "build_models");

        if ($req instanceof BuildModelsFakeRequest) {
            $xmlRoot->setAttribute("fake", $req->getDuration().":".$req->getInterval());

            return;
        }

        // write dataset
        if (!$req->getDataset()->isEmpty()) {
            $xmlDataset = $this->createElement("dataset");
            $xmlRoot->appendChild($xmlDataset);
            $writer = new DatasetWriter($this->getDocument(), $xmlDataset);
            $writer->build($req->getDataset());
        }

        // write agings
        if (!$req->getAging()->isEmpty()) {
            $xmlAgings = $this->createElement("agings");
            $xmlRoot->appendChild($xmlAgings);
            foreach ($req->getAging() as $col=>$colAgings) {
                $xmlColAgings = $this->createElement("column");
                $xmlColAgings->setAttribute("name", $col);
                $xmlAgings->appendChild($xmlColAgings);
                foreach ($colAgings as $pos=>$aging) {
                    $xmlAging = $this->createElement("aging");
                    $xmlAging->setAttribute("position", $pos);
                    $xmlColAgings->appendChild($xmlAging);
                    $writer = new AgingWriter($this->getDocument(), $xmlAging);
                    $writer->build($aging);
                }
            }
        }
    }
}

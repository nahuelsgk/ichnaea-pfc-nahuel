<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\BuildModelsRequest;

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
        $xmlDataset = $this->createElement("dataset");
        $xmlRoot->appendChild($xmlDataset);
        $writer = new DatasetWriter($this->getDocument(), $xmlDataset);
        $writer->build($req->getDataset());

        // write seasons
        $xmlSeasons = $this->createElement("seasons");
        $xmlRoot->appendChild($xmlSeasons);
        foreach($req->getSeasons() as $col=>$colSeasons) {
            $xmlColSeasons = $this->createElement("column");
            $xmlColSeasons->setAttribute("name", $col);
            $xmlSeasons->appendChild($xmlColSeasons);
            foreach($colSeasons as $pos=>$season) {
                $xmlSeason = $this->createElement("season");
                $xmlSeason->setAttribute("position", $pos);
                $xmlColSeasons->appendChild($xmlSeason);
                $writer = new SeasonWriter($this->getDocument(), $xmlSeason);
                $writer->build($season);
            }
        }
    }
}

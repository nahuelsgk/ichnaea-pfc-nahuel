<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\BuildModelsRequest;

class BuildModelsRequestWriter extends Writer
{
    public function __construct($document=null, $root="request")
    {
        parent::__construct($document, $root);
    }

    public function build(BuildModelsRequest $req)
    {
        $xmlRoot = $this->getRoot();
        $xmlRoot->setAttribute("id", $req->getId());
        $xmlRoot->setAttribute("type", "build_models");

        if ($req->isFake()) {
            $xmlRoot->setAttribute("fake", $req->getFake());
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

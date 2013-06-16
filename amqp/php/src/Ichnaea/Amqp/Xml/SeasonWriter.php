<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\Season;

class SeasonWriter extends Writer
{
    public function __construct($document=null, $root="season")
    {
        parent::__construct($document, $root);
    }

    public function build(Season $season)
    {
        $xmlRoot = $this->getRoot();
        foreach ($season as $trial) {
            $xmlCol = $this->createElement("trial");
            $xmlRoot->appendChild($xmlCol);
            foreach ($trial as $key=>$value) {
                $xmlVal = $this->createElement("value", $value);
                $xmlVal->setAttribute("key", $key);
                $xmlCol->appendChild($xmlVal);
            }
        }
    }
}

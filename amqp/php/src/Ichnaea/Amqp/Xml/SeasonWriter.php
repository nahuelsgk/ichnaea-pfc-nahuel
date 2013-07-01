<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\Season;

/**
 * An XML writer that writes Season objects to xml
 *
 * @see Ichnaea\Amqp\Model\Season
 * @author Miguel Ibero <miguel@ibero.me>
 */
class SeasonWriter extends Writer
{
    /**
     * Constructor
     *
     * @param \DOMDocument the document to use
     * @param \DOMElement the element to use as root
     */    
    public function __construct($document=null, $root="season")
    {
        parent::__construct($document, $root);
    }

    /**
     * Write the season data into the xml
     *
     * @param Season the season object
     */
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

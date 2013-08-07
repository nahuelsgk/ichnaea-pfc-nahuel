<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\Aging;

/**
 * An XML writer that writes Aging objects to xml
 *
 * @see Ichnaea\Amqp\Model\Aging
 * @author Miguel Ibero <miguel@ibero.me>
 */
class AgingWriter extends Writer
{
    /**
     * Constructor
     *
     * @param \DOMDocument the document to use
     * @param \DOMElement the element to use as root
     */    
    public function __construct($document=null, $root="aging")
    {
        parent::__construct($document, $root);
    }

    /**
     * Write the aging data into the xml
     *
     * @param Aging the aging object
     */
    public function build(Aging $aging)
    {
        $xmlRoot = $this->getRoot();
        foreach ($aging as $trial) {
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

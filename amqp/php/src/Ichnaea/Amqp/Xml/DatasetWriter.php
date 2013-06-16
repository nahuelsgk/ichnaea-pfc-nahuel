<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\Dataset;

/**
 * An XML writer that writes Dataset objects to xml
 *
 * @see Ichnaea\Amqp\Model\Dataset
 * @author Miguel Ibero <miguel@ibero.me>
 */
class DatasetWriter extends Writer
{
    /**
     * Constructor
     *
     * @param \DOMDocument the document to use
     * @param \DOMElement the element to use as root
     */    
    public function __construct($document=null, $root="dataset")
    {
        parent::__construct($document, $root);
    }

    /**
     * Write the dataset data into the xml
     *
     * @param Dataset the dataset object
     */
    public function build(Dataset $dataset)
    {
        $xmlRoot = $this->getRoot();
        foreach ($dataset as $name=>$values) {
            $xmlCol = $this->createElement("column");
            $xmlRoot->appendChild($xmlCol);
            $xmlCol->setAttribute("name", $name);
            foreach ($values as $value) {
                $xmlCol->appendChild($this->createElement("value", $value));
            }
        }
    }
}

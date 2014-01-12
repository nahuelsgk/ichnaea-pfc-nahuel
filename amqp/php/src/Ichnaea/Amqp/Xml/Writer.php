<?php

namespace Ichnaea\Amqp\Xml;

/**
 * Generic xml writer used as the base for all writers in the library
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
abstract class Writer
{
    /**
     * The dom document used to create the elements
     *
     * @var \DOMDocument
     */
    private $document;

    /**
     * The dom element used as root
     *
     * @var \DOMElement
     */
    private $root;

    /**
     * Constructor
     *
     * @param \DOMDocument the document to use
     * @param \DOMElement the element to use as root
     */
    public function __construct($document=null, $root="document")
    {
        if (is_string($document)) {
            $root = $document;
        }
        if (!$document instanceof \DOMDocument) {
            $document = new \DOMDocument();
        }
        $this->document = $document;
        if (is_string($root)) {
            $root = $document->createElement($root);
            $document->appendChild($root);
        }
        $this->root = $root;
    }

    /**
     * Return the xml string of the document
     *
     * @return string the xml string
     */
    public function __toString()
    {
        return $this->document->saveXML();
    }

    /**
     * Return the document
     *
     * @return \DOMDocument the document
     */
    protected function getDocument()
    {
        return $this->document;
    }

    /**
     * Return the root element
     *
     * @return \DOMElement the root element
     */
    protected function getRoot()
    {
        return $this->root;
    }

    /**
     * Create a new element
     *
     * @param string $name  name of the element tag
     * @param string $value contents of the element tag
     */
    protected function createElement($name, $value="")
    {
        return $this->document->createElement($name, $value);
    }

    /**
     * Add A child to the root element
     *
     * @param mixed the child or the child tag name
     * @param string the child value
     * @return \DOMNode the node to the child
     */
    protected function appendChild($child, $value="")
    {
        if (is_string($child)) {
            $child = $this->createElement($child, $value);
        }

        return $this->root->appendChild($child);
    }
}

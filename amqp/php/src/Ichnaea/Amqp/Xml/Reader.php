<?php

namespace Ichnaea\Amqp\Xml;

/**
 * Generic xml reader used as the base for all readers in the library
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
abstract class Reader
{
    /**
     * Should read an xml data string and return the read object
     *
     * @param string xml data
     * @return mixed the object that was read
     */
    abstract public function read($data);

    /**
     * Finds a root node
     *
     * @paran mixed $data can be a string or a \DomElement
     * @return \DomElement root node with the given name
     */
    protected function getRootNode($data, $name)
    {
    	$rootNode = null;
        if($data instanceof \DomElement) {
            $rootNode = $data;
        } else if(is_string($data)) {
            $xml = new \DOMDocument();
            $xml->loadXML($data);
            foreach ($xml->childNodes as $node) {
                if ($node->nodeName === $name) {
                    $rootNode = $node;
                    break;
                }
            }
        }
        if($rootNode == null) {
        	throw new \InvalidArgumentException("Could not find root node with name ".$name.".");
        }

        return $rootNode;
    }
}

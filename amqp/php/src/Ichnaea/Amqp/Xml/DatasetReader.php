<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\Dataset;

/**
 * Xml reader that reads Dataset objects
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class DatasetReader extends Reader
{
    /**
     * Read a dataset object
     *
     * @param  mixed $data the response data string or a \DomElement
     * @return Dataset a dataset object
     */
    public function read($data)
    {       
        $rootNode = null;
        if($data instanceof \DomElement) {
            $rootNode = $data;
        } else {
            $xml = new \DOMDocument();
            $xml->loadXML($data);
            foreach ($xml->childNodes as $node) {
                if ($node->nodeName === 'dataset') {
                    $rootNode = $node;
                    break;
                }
            }
        }

        if($rootNode == null) {
            return null;
        }

        $cols = array();
        foreach($rootNode->childNodes as $node) {
            if($node->nodeName === 'column') {
                $name = $node->getAttribute('name');
                $cols[$name] = array();
                foreach($node->childNodes as $node) {
                    if($node->nodeName === 'value') {
                        $cols[$name][] = $node->nodeValue;
                    }
                }
            }
        }
        return new Dataset($cols);
    }
}

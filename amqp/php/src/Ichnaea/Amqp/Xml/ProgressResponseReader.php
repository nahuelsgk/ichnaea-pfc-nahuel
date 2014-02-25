<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\ProgressResponse;

/**
 * Xml reader that reads ProgressResponse objects
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class ProgressResponseReader extends Reader
{
    /**
     * Read a response object
     *
     * @param  string $data the response data
     * @return ProgressResponse a response object
     */
    public function read($data)
    {
        $resp = null;        
        $xml = new \DOMDocument();
        $xml->loadXML($data);

        foreach ($xml->childNodes as $node) {
            if ($node->nodeName === 'response') {
                $resp = new ProgressResponse($node->getAttribute('id'));
                $resp->setProgress($node->getAttribute('progress'));
                $resp->setStart($node->getAttribute('start'));
                $resp->setEnd($node->getAttribute('end'));
                $resp->setError($node->getAttribute('error'));
            }
        }
        return $resp;
    }
}

<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\TestModelsResponse;
use Ichnaea\Amqp\Mime\MimeMultipart;
use Ichnaea\Amqp\Mime\MimePart;

/**
 * Xml reader that reads TestModelsResponse objects
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class TestModelsResponseReader extends Reader
{
    /**
     * Read a response object
     *
     * @param  string              $data the response data
     * @return TestModelsResponse a response object
     */
    public function read($data)
    {
        $resp = null;
        
        if ($xmlPart instanceof MimePart) {
            $xml = new \DOMDocument();
            $xml->loadXML($data);

            foreach ($xml->childNodes as $node) {
                if ($node->nodeName === 'response') {
                    $resp = new TestModelsResponse($node->getAttribute('id'));
                    $resp->setProgress($node->getAttribute('progress'));
                    $resp->setStart($node->getAttribute('start'));
                    $resp->setEnd($node->getAttribute('end'));
                    $resp->setError($node->getAttribute('error'));
                }
            }
        }

        return $resp;
    }
}

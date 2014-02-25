<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\PredictModelsResponse;
use Ichnaea\Amqp\Mime\MimeMultipart;
use Ichnaea\Amqp\Mime\MimePart;

/**
 * Xml reader that reads PredictModelsResponse objects
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class PredictModelsResponseReader extends Reader
{
    /**
     * Read a response object
     *
     * @param  string $data the response data
     * @return PredictModelsResponse a response object
     */
    public function read($data)
    {
        $resp = null;
        
        if ($xmlPart instanceof MimePart) {
            $xml = new \DOMDocument();
            $xml->loadXML($data);

            foreach ($xml->childNodes as $node) {
                if ($node->nodeName === 'response') {
                    $resp = new PredictModelsResponse($node->getAttribute('id'));
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

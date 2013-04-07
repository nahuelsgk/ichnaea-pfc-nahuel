<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\BuildModelsResponse;
use Ichnaea\Amqp\Mime\MimeMultipart;
use Ichnaea\Amqp\Mime\MimePart;

class BuildModelsResponseReader extends Reader
{
    public function read($data)
    {
        $resp = null;
        $multi = MimeMultipart::fromString($data);

        $xmlPart = $multi->getPart(0);
        if ($xmlPart instanceof MimePart) {
            $xml = new \DOMDocument();
            $xml->loadXML($xmlPart->getBody());

            foreach ($xml->childNodes as $node) {
                if ($node->nodeName === 'response') {
                    $resp = new BuildModelsResponse($node->getAttribute('id'));
                    $resp->setProgress($node->getAttribute('progress'));
                    $resp->setStart($node->getAttribute('start'));
                    $resp->setEnd($node->getAttribute('end'));
                    $resp->setError($node->getAttribute('error'));
                }
            }
        }

        if ($resp) {
            $dataPart = $multi->getPart(1);
            if ($dataPart instanceof MimePart) {
                $resp->setData($dataPart->getBody());
            }
        }

        return $resp;
    }
}

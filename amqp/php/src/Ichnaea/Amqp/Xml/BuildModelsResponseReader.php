<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\BuildModelsResponse;
use Ichnaea\Amqp\Model\ProgressResponse;
use Ichnaea\Amqp\Mime\MimeMultipart;
use Ichnaea\Amqp\Mime\MimePart;

/**
 * Xml reader that reads BuildModelsResponse objects
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class BuildModelsResponseReader extends ProgressResponseReader
{

    /**
     * Read a response object
     *
     * @param  string              $data the response data
     * @return BuildModelsResponse a response object
     */
    public function read($data)
    {
        $resp = null;
        $multi = MimeMultipart::fromString($data);

        $xmlPart = $multi->getPart(0);
        if ($xmlPart instanceof MimePart) {
            $progResp = parent::read($xmlPart->getBody());
            if($progResp instanceof ProgressResponse) {
                $resp = PredictModelsResponse::fromArray($resp->toArray());
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

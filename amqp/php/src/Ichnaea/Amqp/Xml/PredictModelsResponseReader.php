<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\PredictModelsResponse;
use Ichnaea\Amqp\Model\ProgressResponse;
use Ichnaea\Amqp\Mime\MimeMultipart;
use Ichnaea\Amqp\Mime\MimePart;

/**
 * Xml reader that reads PredictModelsResponse objects
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class PredictModelsResponseReader extends ProgressResponseReader
{
    /**
     * Read a response object
     *
     * @param  string $data the response data
     * @return PredictModelsResponse a response object
     */
    public function read($data)
    {
        $resp = parent::read($data);
        if($resp instanceof ProgressResponse) {
            return PredictModelsResponse::fromArray($resp->toArray());
        }
        return null;
    }
}
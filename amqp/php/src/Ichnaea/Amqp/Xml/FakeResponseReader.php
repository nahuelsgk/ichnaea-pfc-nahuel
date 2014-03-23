<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\FakeResponse;
use Ichnaea\Amqp\Model\ProgressResponse;
use Ichnaea\Amqp\Mime\MimeMultipart;
use Ichnaea\Amqp\Mime\MimePart;

/**
 * Xml reader that reads FakeResponse objects
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class FakeResponseReader extends ProgressResponseReader
{
    /**
     * Read a response object
     *
     * @param  string $data the response data
     * @return FakeResponse a response object
     */
    public function read($data)
    {
        $resp = parent::read($data);
        if($resp instanceof ProgressResponse) {
            return FakeResponse::fromArray($resp->toArray());
        }
        return null;
    }
}

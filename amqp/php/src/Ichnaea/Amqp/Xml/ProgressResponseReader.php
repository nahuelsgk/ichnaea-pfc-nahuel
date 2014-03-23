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
     * @param  string           $data the response data
     * @return ProgressResponse a response object
     */
    public function read($data)
    {
        $rootNode = $this->getRootNode($data, 'response');
        $resp = new ProgressResponse($rootNode->getAttribute('id'));
        $resp->setProgress($rootNode->getAttribute('progress'));
        $resp->setStart($rootNode->getAttribute('start'));
        $resp->setEnd($rootNode->getAttribute('end'));
        $resp->setError($rootNode->getAttribute('error'));

        return $resp;
    }
}

<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\PredictModelsResponse;
use Ichnaea\Amqp\Model\ProgressResponse;

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
     * @param  string                $data the response data
     * @return PredictModelsResponse a response object
     */
    public function read($data)
    {
        $rootNode = $this->getRootNode($data, 'response');
        $resp = parent::read($rootNode);
        if ($resp instanceof ProgressResponse) {
            $resp = PredictModelsResponse::fromArray($resp->toArray());
            foreach ($rootNode->childNodes as $node) {
                if ($node->nodeName === 'result') {
                    $reader = new PredictModelsResultReader();
                    $resp->setResult($reader->read($node));
                }
            }

            return $resp;
        }

        return null;
    }
}

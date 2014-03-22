<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\PredictModelsResult;
use Ichnaea\Amqp\Model\ProgressResponse;


/**
 * Xml reader that reads PredictModelsResponse objects
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class PredictModelsResultReader extends Reader
{
    /**
     * Read a response object
     *
     * @param  string $data the response data
     * @return PredictModelsResponse a response object
     */
    public function read($data)
    {
        $result = null;        
        $xml = new \DOMDocument();
        $xml->loadXML($data);

        foreach ($xml->childNodes as $node) {
            if ($node->nodeName === 'result') {
                $result = new PredictModelsResult();
                $result->setName($node->getAttribute('name'));
                $result->setTestError($node->getAttribute('testError'));
                $result->setPredictedSamples($node->getAttribute('predictedSamples'));
                $result->setTotalSamples($node->getAttribute('totalSamples'));
                foreach($node->childNodes as $node) {
                    if($node->nodeName === 'dataset') {
                        $reader = new DatasetReader();
                        $result->setDataset($reader->read($node));
                    }
                    if($node->nodeName === 'confusionMatrix') {
                        $reader = new DatasetReader();
                        $result->setConfusionMatrix($reader->read($node));
                    }
                }
            }
        }
        return $result;
    }
}

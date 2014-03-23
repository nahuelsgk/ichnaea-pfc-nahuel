<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\PredictModelsResult;

/**
 * Xml reader that reads PredictModelsResult objects
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class PredictModelsResultReader extends Reader
{
    /**
     * Read a result object
     *
     * @param  mixed               $data the response data string or a \DomElement
     * @return PredictModelsResult a result object
     */
    public function read($data)
    {
        $rootNode = $this->getRootNode($data, 'result');
        $result = new PredictModelsResult();
        $result->setName($rootNode->getAttribute('name'));
        $result->setTestError($rootNode->getAttribute('testError'));
        $result->setPredictedSamples($rootNode->getAttribute('predictedSamples'));
        $result->setTotalSamples($rootNode->getAttribute('totalSamples'));
        foreach ($rootNode->childNodes as $node) {
            if ($node->nodeName === 'dataset') {
                $reader = new DatasetReader();
                $result->setDataset($reader->read($node));
            }
            if ($node->nodeName === 'confusionMatrix') {
                $reader = new DatasetReader();
                $result->setConfusionMatrix($reader->read($node));
            }
        }

        return $result;
    }
}

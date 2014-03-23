<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a predict models result.
 *
 * This result contains:
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class PredictModelsResult
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Dataset
     */
    private $dataset;

    /**
     * The error introduced by the prediction
     * 1.0f means 100%
     * @var float
     */
    private $testError;

    /**
     * The amount of samples predicted
     * @var int
     */
    private $predictedSamples;

    /**
     * The total amount of samples
     * @var int
     */
    private $totalSamples;

    /**
     * The confusion matrix
     * @var array
     */
    private $confusionMatrix;

    public function __construct()
    {
        $this->name = "";
        $this->dataset = new Dataset();
        $this->confusionMatrix = new Dataset();
        $this->testError = 0.0;
        $this->predictedSamples = 0;
        $this->totalSamples = 0;
    }

    /**
     * Set the data for the build models columns
     * The data can be a Dataset object or
     * a dataset \SplFileObject, a string with csv data
     * or an array.
     *
     * @param mixed $dataset the data
     */
    public function setDataset($dataset)
    {
        if (!$dataset instanceof Dataset) {
            $dataset = new Dataset($dataset);
        }
        $this->dataset = $dataset;
    }

    /**
     * Get the dataset
     *
     * @return Dataset the dataset
     */
    public function getDataset()
    {
        return $this->dataset;
    }

    /**
     * Set the name of the result
     * 
     * @param string $name the name of the result
     */
    public function setName($name)
    {
        $this->name = strval($name);
    }

    /**
     * Get the dataset
     *
     * @return string the name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the test error the result
     * 1.0f means 100%
     * 
     * @param float $error the error value
     */
    public function setTestError($error)
    {
        $this->testError = floatval($error);
    }

    /**
     * Get the test error
     *
     * @return string the test error
     */
    public function getTestError()
    {
        return $this->testError;
    }    

    /**
     * Set the predicted samples the result
     * 
     * @param int $samples the amount of samples predicted
     */
    public function setPredictedSamples($samples)
    {
        $this->predictedSamples = intval($samples);
    }

    /**
     * Get the predicted samples
     *
     * @return int the predicted samples
     */
    public function getPredictedSamples()
    {
        return $this->predictedSamples;
    }

    /**
     * Set the total samples the result
     * 
     * @param int $samples the total amount of samples
     */
    public function setTotalSamples($samples)
    {
        $this->totalSamples = intval($samples);
    }

    /**
     * Get the total samples
     *
     * @return int the total samples
     */
    public function getTotalSamples()
    {
        return $this->totalSamples;
    }

    /**
     * Set the confusion matrix of the result
     * 
     * @param \Dataset $matrix the confusion matrix
     */
    public function setConfusionMatrix($matrix)
    {
        if (!$matrix instanceof Dataset) {
            $matrix = new Dataset($matrix);
        }
        $this->confusionMatrix = $matrix;
    }

    /**
     * Get the confusion matrix
     *
     * @return array the confusion matrix
     */
    public function getConfusionMatrix()
    {
        return $this->confusionMatrix;
    }            

    /**
     * Export the response to an array
     *
     * @return array the response data
     */
    public function toArray()
    {
        $a = array(
            'name'              => $this->getName(),
            'dataset'           => $this->getDataset()->toArray(),
            'testError'         => $this->getTestError(),
            'predictedSamples'  => $this->getPredictedSamples(),
            'totalSamples'      => $this->getTotalSamples(),
            'confusionMatrix'   => $this->getConfusionMatrix()->toArray(),
        );
        return $a;
    }

    /**
     * Update the response from an array
     *
     * @param array the response data
     */
    public function update(array $data)
    {
        if(array_key_exists('name', $data)) {
            $this->setName($data['name']);
        }
        if(array_key_exists('dataset', $data)) {
            $this->setDataset($data['dataset']);
        }
        if(array_key_exists('testError', $data)) {
            $this->setTestError($data['testError']);
        }        
        if(array_key_exists('predictedSamples', $data)) {
            $this->setPredictedSamples($data['predictedSamples']);
        }
        if(array_key_exists('totalSamples', $data)) {
            $this->setTotalSamples($data['totalSamples']);
        }        
        if(array_key_exists('confusionMatrix', $data)) {
            $this->setConfusionMatrix($data['confusionMatrix']);
        }                
    }

    /**
     * Create the response from an array
     *
     * @param array the response data
     * @return PredictModelsResponse the response
     */
    public static function fromArray(array $data)
    {
        $data = array_merge(array('id'=>null), $data);
        $resp = new self($data['id']);
        $resp->update($data);

        return $resp;
    }

    /**
     * Return the result data as html
     * @return string the html
     **/
    public function toHtml()
    {
        $html = "<div class=\"ichnaea_predict_models_result\">";
        $html .= "<h3>".$this->getName()."</h3>\n";
        $html .= "<h4>Dataset</h4>\n";
        $html .= $this->getDataset()->toHtml()."\n";
        $html .= "<h4>Confusion Matrix</h4>\n";
        $html .= $this->getConfusionMatrix()->toHtml()."\n";
        $html .= "<ul>\n";
        $html .= "<li>".$this->getPredictedSamples() . "/" . $this->getTotalSamples() . " samples</li>\n";
        $html .= "<li>". $this->getTestError()*100 ."% error</li>\n";
        $html .= "</ul>\n";
        $html .= "</div>\n";
        return $html;
    }

    /**
     * Return true if there is no data in the result
     * @return bool true if empty
     */
    public function isEmpty()
    {
        return $this->getPredictedSamples() == 0;
    }

    /**
     * Return true if the result is finished
     * @return bool true if finished
     */
    public function isFinished()
    {
        return !$this->isEmpty() &&
            $this->getPredictedSamples() == $this->getTotalSamples() &&
            !$this->getDataset()->isEmpty();
    }    
}

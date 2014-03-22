<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a predict models response.
 *
 * This response contains:
 * * a PredictModelsResult
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class PredictModelsResponse extends ProgressResponse
{
    /**
     * The result
     *
     * @var PredictModelsResult
     */
    private $result;

    /**
     * Constructor for the predict_models response
     *
     * @param string $id identifier for the request
     */
    public function __construct($id=null)
    {
        parent::__construct($id);
        $this->result = new PredictModelsResult();
    }

    /**
     * Get the predict models result
     *
     * @return PredictModelsResult the result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set a predict models result
     *
     * @param mixed $result can be a PredictModelsResult or an array
     */
    public function setResult($result)
    {
        if(is_array($result)) {
            $result = PredictModelsResult::fromArray($result);
        }
        if(!$result instanceof PredictModelsResult) {
            throw new \InvalidArgumentException("Invalid result.");
        }
        $this->result = $result;
    }

    /**
     * Export the response to an array
     *
     * @return array the response data
     */
    public function toArray()
    {
        $a = parent::toArray();
        $a['result'] = $this->getResult()->toArray();
        return $a;
    }

    /**
     * Update the response from an array
     *
     * @param array the response data
     */
    public function update(array $data)
    {
        parent::update($data);
        if(array_key_exists('result', $data)) {
            $this->setResult($data['result']);
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
}

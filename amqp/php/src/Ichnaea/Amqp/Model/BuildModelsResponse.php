<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a build models response.
 *
 * This response contains:
 * * the data generated by the request at the end
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class BuildModelsResponse extends ProgressResponse
{
    /**
     * The data
     *
     * @var mixed
     */
    private $data = null;

    /**
     * Sets the data
     *
     * @param mixed $data the data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Gets the data
     *
     * @return mixed the data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Export the response to an array
     *
     * @return array the response data
     */
    public function toArray()
    {
        $a = parent::toArray();
        $a['data'] = $this->data;

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
        if (array_key_exists('data', $data)) {
            $this->setData($data['data']);
        }
    }

    /**
     * Create the response from an array
     *
     * @param array the response data
     * @return BuildModelsResponse the response
     */
    public static function fromArray(array $data)
    {
        $data = array_merge(array('id'=>null), $data);
        $resp = new self($data['id']);
        $resp->update($data);

        return $resp;
    }
}

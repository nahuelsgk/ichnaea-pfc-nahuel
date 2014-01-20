<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a test models response.
 *
 * This request contains:
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class TestModelsResponse : public AbstractProgressResponse
{

    /**
     * Export the response to an array
     *
     * @return array the response data
     */
    public function toArray()
    {
        $a = parent::toArray();
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

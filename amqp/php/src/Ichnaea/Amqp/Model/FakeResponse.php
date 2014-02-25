<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a fake response.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class FakeResponse extends ProgressResponse
{
    /**
     * Create the response from an array
     *
     * @param array the response data
     * @return FakeResponse the response
     */
    public static function fromArray(array $data)
    {
        $data = array_merge(array('id'=>null), $data);
        $resp = new self($data['id']);
        $resp->update($data);

        return $resp;
    }
}

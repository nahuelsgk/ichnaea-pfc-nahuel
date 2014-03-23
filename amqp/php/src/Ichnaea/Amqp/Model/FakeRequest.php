<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a build models fake request
 *
 * It is used to test the amqp server
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class FakeRequest
{
    /**
     * @var string
     */
    private $id;

    /**
     * The duration of the request in seconds
     *
     * @var float
     */
    private $duration;

    /**
     * The interval for updates in seconds
     *
     * @var float
     */
    private $interval;

    /**
     * Constructor for the fake request
     *
     * @param string $id identifier for the request
     */
    public function __construct($id=null)
    {
        if (!$id) {
            $id = uniqid();
        }
        $this->id = $id;
    }

    /**
     * Get the request id
     *
     * @return string the request id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the duration
     *
     * @param float $duration the duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * Set the interval
     *
     * @param float $duration the duration
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    /**
     * Get the duration
     *
     * @return float the duration
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Get the interval
     *
     * @return float the interval
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Export the request to an array
     *
     * @return array the request data
     */
    public function toArray()
    {
        return array(
            "id"            => $this->getId(),
            "duration"      => $this->duration,
            "interval"      => $this->interval,
        );
    }

    /**
     * Update the request from an array
     *
     * @param array the request data
     */
    public function update(array $data)
    {
        if (array_key_exists('duration', $data)) {
            $this->setDuration($data['duration']);
        }
        if (array_key_exists('interval', $data)) {
            $this->setInterval($data['interval']);
        }
    }

    /**
     * Create the request from an array
     *
     * @param array the request data
     * @return BuildModelsFakeRequest the request
     */
    public static function fromArray(array $data)
    {
        $data = array_merge(array('id'=>null), $data);
        $req = new self($data['id']);
        $req->update($data);

        return $req;
    }

}

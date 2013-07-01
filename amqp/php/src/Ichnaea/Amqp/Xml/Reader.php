<?php

namespace Ichnaea\Amqp\Xml;

/**
 * Generic xml reader used as the base for all readers in the library
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
abstract class Reader
{
    /**
     * Should read an xml data string and return the read object
     *
     * @param string xml data
     * @return mixed the object that was read
     */
    abstract public function read($data);
}

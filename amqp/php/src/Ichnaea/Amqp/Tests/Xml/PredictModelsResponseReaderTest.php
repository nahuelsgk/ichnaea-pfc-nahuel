<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\Dataset;
use Ichnaea\Amqp\Xml\PredictModelsResponseReader;

date_default_timezone_set(@date_default_timezone_get());

class PredictModelsResponseReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testReadingXml()
    {
        $xml = '<response end="2014-03-23T14:56:18.735+0100" id="532ee7fe9d2f1" progress="1.0" ';
        $xml .= 'start="2014-03-23T14:56:16.830+0100" type="predict_models"/>';
        
        $reader = new PredictModelsResponseReader();
        $result = $reader->read($xml);
    }
}
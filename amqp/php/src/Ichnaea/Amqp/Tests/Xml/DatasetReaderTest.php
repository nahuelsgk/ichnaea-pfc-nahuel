<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\Dataset;
use Ichnaea\Amqp\Xml\DatasetReader;

class DatasetReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testReadingXml()
    {
        $xml = "<dataset>\n";
        $xml .= "<column name=\"test\"><value>1</value><value>2</value><value>3</value></column>\n";
        $xml .= "<column name=\"test2\"><value>3</value><value>4</value></column>\n";
        $xml .= "<column name=\"test3\"><value>5</value><value>6</value><value>7</value></column>\n";
        $xml .= "</dataset>";

        $reader = new DatasetReader();
        $dataset = $reader->read($xml);

        $this->assertEquals(6, $dataset->getColumn("test3")[1], "DatasetReader reads the data correctly.");
    }
}

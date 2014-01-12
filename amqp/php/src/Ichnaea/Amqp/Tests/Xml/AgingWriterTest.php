<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\Aging;
use Ichnaea\Amqp\Xml\AgingWriter;

class AgingWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWritingXml()
    {
        $aging = new Aging();
        $aging->setTrials(array(
            array(
                0   => 4.4,
                10  => 40,
                50  => 120.6
            ),
            array(
                0   => 5.4,
                10  => 43,
                50  => 123.6
            )
        ));

        $xml = new AgingWriter();
        $xml->build($aging);

        $expectedXml  = '<aging><trial><value key="0">4.4</value><value key="10">40</value>';
        $expectedXml .= '<value key="50">120.6</value></trial><trial><value key="0">5.4</value>';
        $expectedXml .= '<value key="10">43</value><value key="50">123.6</value></trial></aging>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $xml, "Aging xml writer exports aging data.");
    }
}

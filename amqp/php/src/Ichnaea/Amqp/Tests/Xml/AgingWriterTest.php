<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\Aging;
use Ichnaea\Amqp\Xml\AgingWriter;

class AgingWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWritingXml()
    {
        $aging = new Aging(array(
            array(
                0   => 6.91,
                24  => 6.05,
                48  => 5.99,
                72  => 4.60,
            ),
            array(
                0   => 6.91,
                24  => 6.05,
                48  => 5.99,
                72  => 4.52,
            )
        ));

        $writer = new AgingWriter();
        $writer->build($aging);

        $expectedXml = '<aging><trial><value key="0">6.91</value><value key="24">6.05</value>';
        $expectedXml .= '<value key="48">5.99</value><value key="72">4.6</value></trial><trial>';
        $expectedXml .= '<value key="0">6.91</value><value key="24">6.05</value><value key="48">5.99</value>';
        $expectedXml .= '<value key="72">4.52</value></trial></aging>';
        $xml = $writer->__toString();

        $this->assertXmlStringEqualsXmlString($expectedXml, $xml, "Aging xml writer exports aging data.");

    }
}

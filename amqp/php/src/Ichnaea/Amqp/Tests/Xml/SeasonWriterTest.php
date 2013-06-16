<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\Season;
use Ichnaea\Amqp\Xml\SeasonWriter;

class SeasonWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWritingXml()
    {
        $season = new Season();
        $season->setTrials(array(
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

        $xml = new SeasonWriter();
        $xml->build($season);

        $expectedXml  = '<season><trial><value key="0">4.4</value><value key="10">40</value>';
        $expectedXml .= '<value key="50">120.6</value></trial><trial><value key="0">5.4</value>';
        $expectedXml .= '<value key="10">43</value><value key="50">123.6</value></trial></season>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $xml, "Season xml writer exports season data.");
    }
}
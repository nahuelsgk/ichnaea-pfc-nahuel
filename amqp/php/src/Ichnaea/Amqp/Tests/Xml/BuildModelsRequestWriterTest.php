<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\BuildModelsRequest;
use Ichnaea\Amqp\Model\BuildModelsFakeRequest;
use Ichnaea\Amqp\Xml\BuildModelsRequestWriter;

class BuildModelsRequestWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWritingXml()
    {
        $req = new BuildModelsRequest(1);
        $req->setDataset(array(
            "BA"    => array(1, 2, 3, 4, 5),
            "CNFC"  => array(1, 2, 3, 5, 6),
        ));
        $req->getAging()->setAging("BA", 0.5, array(
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

        $xml = new BuildModelsRequestWriter();
        $xml->build($req);

        $expectedXml  = '<request id="1" type="build_models"><dataset>';
        $expectedXml .= '<column name="BA"><value>1</value><value>2</value><value>3</value><value>4</value><value>5</value></column>';
        $expectedXml .= '<column name="CNFC"><value>1</value><value>2</value><value>3</value><value>5</value><value>6</value></column>';
        $expectedXml .= '</dataset><agings><column name="BA"><aging position="0.5"><trial>';
        $expectedXml .= '<value key="0">4.4</value><value key="10">40</value><value key="50">120.6</value></trial>';
        $expectedXml .= '<trial><value key="0">5.4</value><value key="10">43</value><value key="50">123.6</value>';
        $expectedXml .= '</trial></aging></column></agings></request>';
        
        $this->assertXmlStringEqualsXmlString($expectedXml, $xml, "BuildModelsRequest xml writer exports request data.");
    }

    public function testWritingFakeXml()
    {
        $req = new BuildModelsFakeRequest(2);
        $req->setDuration(10);
        $req->setInterval(1);

        $xml = new BuildModelsRequestWriter();
        $xml->build($req);

        $expectedXml = '<request id="2" fake="10:1" type="build_models"></request>';

        $this->assertXmlStringEqualsXmlString($expectedXml, $xml, "BuildModelsRequest xml writer exports fake request data.");
    }
}
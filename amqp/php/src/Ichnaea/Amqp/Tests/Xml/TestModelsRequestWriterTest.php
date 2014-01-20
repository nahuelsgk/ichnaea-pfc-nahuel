<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\TestModelsRequest;
use Ichnaea\Amqp\Xml\TestModelsRequestWriter;

class TestModelsRequestWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWritingXml()
    {
        $req = new TestModelsRequest(1);

        $xml = new TestModelsRequestWriter();
        $xml->build($req);

        $expectedXml  = '<request id="1" type="test_models"></request>';

        $this->assertXmlStringEqualsXmlString($expectedXml, $xml, "TestModelsRequest xml writer exports request data.");
    }
}

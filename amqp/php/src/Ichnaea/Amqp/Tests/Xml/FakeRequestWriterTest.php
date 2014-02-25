<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\FakeRequest;
use Ichnaea\Amqp\Xml\FakeRequestWriter;

class FakeRequestWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWritingXml()
    {
        $req = new FakeRequest(2);
        $req->setDuration(10);
        $req->setInterval(1);

        $xml = new FakeRequestWriter();
        $xml->build($req);

        $expectedXml = '<request id="2" duration="10" interval="1" type="fake"></request>';

        $this->assertXmlStringEqualsXmlString($expectedXml, $xml, "FakeRequest xml writer exports request data.");
    }
}

<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\PredictModelsRequest;
use Ichnaea\Amqp\Xml\PredictModelsRequestWriter;

class PredictModelsRequestWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWritingXml()
    {
        $req = new PredictModelsRequest(1);

        $xml = new PredictModelsRequestWriter();
        $xml->build($req);

        $expectedXml  = '<request id="1" type="predict_models"></request>';

        $this->assertXmlStringEqualsXmlString($expectedXml, $xml, "PredictModelsRequest xml writer exports request data.");
    }
}

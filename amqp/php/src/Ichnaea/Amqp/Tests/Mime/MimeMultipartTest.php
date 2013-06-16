<?php

namespace Ichnaea\Amqp\Tests\Mime;

use Ichnaea\Amqp\Mime\MimeMultipart;

class MimeMultipartTest extends \PHPUnit_Framework_TestCase
{
    public function testFromString()
    {
        $mime = MimeMultipart::fromString("--boundary\r\nName: Value\r\n\r\nbody\r\nlala\r\n--boundary--\r\n");
        $this->assertEquals(1, $mime->getNumParts(), "parts are parsed from string");
        $this->assertEquals("boundary", $mime->getBoundary(), "boundary is parsed from string");
    }

    public function testToString()
    {
        $mime = new MimeMultipart("boundary");
        $mime->addPart("Name: Value\r\n\r\nbody\r\nlala");

        $expected = "--boundary\r\nName: Value\r\n\r\nbody\r\nlala\r\n--boundary--\r\n";
        $this->assertEquals($expected, "".$mime, "exports to string");
    }    

}
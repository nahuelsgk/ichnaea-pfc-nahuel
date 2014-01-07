<?php

namespace Ichnaea\Amqp\Tests\Mime;

use Ichnaea\Amqp\Mime\MimePart;

class MimePartTest extends \PHPUnit_Framework_TestCase
{
    public function testHeaders()
    {
        $part = new MimePart();

        $part->setHeader('Name', 'Value');
        $part->setHeader("Name\n2", "Value\n3");

        $this->assertEquals('Value', $part->getHeader('Name'), 'headers are set.');
        $this->assertEquals(null, $part->getHeader('Name2'), 'headers that do not exist return null.');
        $this->assertEquals(array('Name'=>'Value', 'Name 2'=> 'Value 3'), $part->getHeaders(), 'headers are returned');
    }

    public function testToString()
    {
        $part = new MimePart();

        $part->setHeader('Name', 'Value');
        $part->setBody('body');

        $this->assertEquals("Name: Value\r\n\r\nbody", "".$part, 'exports to string.');
    }

    public function testFromString()
    {
        $part = MimePart::fromString("Name: Value\r\n\r\nbody\r\nlala");

        $this->assertEquals('Value', $part->getHeader('Name'), 'headers are loaded from string.');
        $this->assertEquals("body\r\nlala", $part->getBody(), 'body is loaded from string');
    }

    public function testStringRoundtrip()
    {
        $part = new MimePart();
        $part->setHeader('Name', 'Value');
        $part->setHeader("Name\n2", "Value\n3");
        $part->setBody("lala\nlolo\n\nas");

        $part2 = MimePart::fromString($part);

        $this->assertEquals($part, $part2, "roundtrip from string works");
    }

}

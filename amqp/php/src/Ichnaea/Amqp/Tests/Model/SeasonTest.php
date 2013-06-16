<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\Season;

class SeasonTest extends \PHPUnit_Framework_TestCase
{
    public function testReadingString()
    {
        $season = new Season("# Envelliment BA Estiu (2 assajos)

0    6.91 
24   6.05
#test comment
48   5.99
72   4.60

0    6.91 
24   6.05
48   5.99
72   4.52
");
        $this->assertEquals(2, count($season->getTrials()), "Trials are loaded correctly from string.");
        
    }
}
<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\Aging;

class AgingTest extends \PHPUnit_Framework_TestCase
{
    public function testReadingString()
    {
        $aging = new Aging("# Envelliment BA Estiu (2 assajos)

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
        $this->assertEquals(2, count($aging->getTrials()), "Trials are loaded correctly from string.");

    }

public function testReadingString2()
    {
        $aging = new Aging("# Envelliment SOMCPH Estiu (6 assajos)

0    4.48
24   4.52
72   4.00
168  3.00

0    5.19
24   4.90
72   4.76
144  3.81
192  3.11

0    5.30
24   4.92
72   4.81
144  3.98
168  3.46

0    4.50
24   4.10
72   3.20
168  1.90

0    4.30
24   4.30
72   3.90
144  2.90
192  2.70

0    5.40
24   4.80
48   4.30
72   4.10
96   3.80
168  3.10

");
        $this->assertEquals(6, count($aging->getTrials()), "Trials are loaded correctly from string.");

    }
}

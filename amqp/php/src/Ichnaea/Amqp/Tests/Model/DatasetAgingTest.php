<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\DatasetAging;

class DatasetAgingTest extends \PHPUnit_Framework_TestCase
{
    public function testReadingString()
    {
        $aging = new DatasetAging(array(
        	"files" => array(
	        	"envBA-Estiu.txt" => "# Envelliment BA Estiu (2 assajos)

	0    6.91 
	24   6.05
	#test comment
	48   5.99
	72   4.60

	0    6.91 
	24   6.05
	48   5.99
	72   4.52
	",
				"envBA-Hivern.txt" => "# Envelliment BA Hivern (2 assajos)

	0    7.63 
	24   6.78
	48   6.60
	72   6.11
	168  6.36
	216  6.29
	240  5.81

	0    6.94 
	24   6.12
	48   5.34
	120  5.87",
			),
			"format"	=> "env%column%-%aging%.txt",
			"positions"	=> array(
				"Estiu" => "0.5",
				"Hivern" => "0.0"
			)
		));

        $this->assertEquals(array('BA'), $aging->getColumnNames(), "Dataset aging is loaded correctly from a files array.");
        $this->assertEquals(5.34, $aging->getAging('BA', "0.0")->getValue(1, 48), "Dataset aging loaded has the correct values.");
    }
}
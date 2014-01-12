<?php

namespace Ichnaea\Amqp\Tests\Model;

use Ichnaea\Amqp\Model\Dataset;

class DatasetTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingColumns()
    {
        $data = new Dataset();
        $data->setColumns(array(
            "BA"    => array(1, 2, 3, 4, 5),
            "CNFC"  => array(1, 2, 3, 5, 6),
        ));

        $this->assertEquals(array("BA", "CNFC"), $data->getColumnNames(), "Column names are the keys of the columns array.");
        $this->assertEquals(array(1, 2, 3, 4, 5), $data->getColumn("BA"), "Columns return their row values by name.");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSettingInvalidColumn()
    {
        $data = new Dataset(array("BA"=>5));
    }

    public function testReadingCsv()
    {
        $data = new Dataset("BA; CNFC
1; 1
2; 2
3; 3
4; 5
5; 6");

        $this->assertEquals(array("BA", "CNFC"), $data->getColumnNames(), "Column names are the keys of the columns array.");
        $this->assertEquals(array(1, 2, 3, 5, 6), $data->getColumn("CNFC"), "Columns return their row values by name.");
    }

}

<?php

namespace Ichnaea\Amqp\Tests\Xml;

use Ichnaea\Amqp\Model\Dataset;
use Ichnaea\Amqp\Xml\PredictModelsResultReader;

class PredictModelsResultReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testReadingXml()
    {
        $xml = '<result name="ERROR" predictedSamples="10" testError="0.08" totalSamples="15">';
        $xml .= '<dataset><column name="FC"><value>2.20e+07</value><value>2.20e+07</value><value>1.70e+07</value>';
        $xml .= '<value>2.30e+07</value><value>2.00e+07</value><value>3.20e+07</value><value>1.10e+07</value>';
        $xml .= '<value>8.40e+07</value><value>8.50e+07</value><value>2.00e+08</value><value>1.30e+08</value>';
        $xml .= '<value>5.18e+05</value><value>1.08e+04</value><value>9.73e+06</value><value>3.00e+02</value>';
        $xml .= '</column><column name="FE"><value>1230000</value><value>930000</value><value>1400000</value>';
        $xml .= '<value>3700000</value><value>540000</value><value>860000</value><value>800000</value>';
        $xml .= '<value>930000</value><value>1000000</value><value>740000</value><value>690000</value>';
        $xml .= '<value>6640000</value><value>100</value><value>1710000</value><value>13</value></column>';
        $xml .= '<column name="CL"><value>240000</value><value>340000</value><value>290000</value>';
        $xml .= '<value>3800000</value><value>120000</value><value>250000</value><value>46000</value>';
        $xml .= '<value>400000</value><value>200000</value><value>100000</value><value>78000</value>';
        $xml .= '<value>11700</value><value>6000</value><value>409000</value><value>5000</value></column>';
        $xml .= '<column name="SOMCPH"><value>1.93e+07</value><value>6.60e+06</value><value>1.10e+07</value>';
        $xml .= '<value>7.90e+06</value><value>8.30e+06</value><value>3.00e+07</value><value>1.20e+07</value>';
        $xml .= '<value>4.50e+07</value><value>1.80e+07</value><value>3.30e+07</value><value>1.03e+08</value>';
        $xml .= '<value>1.91e+05</value><value>1.00e+01</value><value>2.37e+06</value><value>6.50e+04</value>';
        $xml .= '</column><column name="FRNAPH"><value>840000</value><value>540000</value><value>840000</value>';
        $xml .= '<value>1400000</value><value>820000</value><value>710000</value><value>7000</value>';
        $xml .= '<value>1700000</value><value>9500000</value><value>520000</value><value>1600000</value>';
        $xml .= '<value>534000</value><value>394000</value><value>2</value><value>450</value></column>';
        $xml .= '</dataset><confusionMatrix><column name="PREDAN"><value>0</value><value>7</value></column>';
        $xml .= '<column name="PRED"><value>0</value><value>8</value></column></confusionMatrix></result>';
        $reader = new PredictModelsResultReader();
        $result = $reader->read($xml);

        $this->assertEquals("ERROR", $result->getName(), "PredictModelsResultReader reads the name correctly.");
        $this->assertEquals(10, $result->getPredictedSamples(), "PredictModelsResultReader reads the predicted samples correctly.");
        $this->assertEquals(15, $result->getTotalSamples(), "PredictModelsResultReader reads the total samples correctly.");
        $this->assertEquals(0.08, $result->getTestError(), "PredictModelsResultReader reads the test error correctly.");
        $dataset = $result->getDataset();
        $this->assertEquals(15, count($dataset->getRows()), "PredictModelsResultReader reads the dataset cols correctly.");
        $this->assertEquals(array(array("0","0"),array("7","8")), $result->getConfusionMatrix()->getRows(), "PredictModelsResultReader reads the confusion matrix correctly.");
    }
}

package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;

import org.custommonkey.xmlunit.Diff;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.DatasetAging;
import edu.upc.ichnaea.amqp.model.DatasetAgingColumn;
import edu.upc.ichnaea.amqp.model.Aging;
import edu.upc.ichnaea.amqp.model.AgingTrial;

import static org.junit.Assert.*;

public class XmlDatasetAgingWriterTest {

    @Test
    public void testEmptyXML() throws ParserConfigurationException, SAXException, IOException
    {
    	DatasetAging agings = new DatasetAging();
    	
    	String xml = new XmlDatasetAgingWriter().build(agings).toString();
        String expectedXml = "<agings></agings>";
        
        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }
    
    @Test
    public void testXML() throws ParserConfigurationException, SAXException, IOException
    {
    	DatasetAging agings = new DatasetAging();
    	DatasetAgingColumn column = new DatasetAgingColumn();
    	Aging aging = new Aging();
    	AgingTrial trial = new AgingTrial();
    	trial.add("0", "4.4");
    	trial.add("10", "40");
    	trial.add("50", "120.6");
    	aging.addTrial(trial);
    	trial = new AgingTrial();
    	trial.add("0", "5.4");
    	trial.add("10", "43");
    	trial.add("50", "123.6");
    	aging.addTrial(trial);
    	trial = new AgingTrial();
    	trial.add("10", "43");
    	trial.add("50", "123.6");
    	aging.addTrial(trial);
    	column.put(0.5f, aging);
    	agings.put("test", column);
    	
    	column = new DatasetAgingColumn();
    	aging = new Aging();
    	aging.addTrial(trial);
    	column.put(0.0f, aging);
    	column.put(0.2f, aging);
    	agings.put("test2", column);
    	
    	String xml = new XmlDatasetAgingWriter().build(agings).toString();
    	String expectedXml = "<agings><column name=\"test2\"><aging position=\"0.0\">";
    	expectedXml += "<trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
    	expectedXml += "</trial></aging><aging position=\"0.2\">";
    	expectedXml += "<trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
    	expectedXml += "</trial></aging></column><column name=\"test\"><aging position=\"0.5\"><trial>";
        expectedXml += "<value key=\"0\">4.4</value><value key=\"10\">40</value>";
        expectedXml += "<value key=\"50\">120.6</value></trial><trial><value key=\"0\">5.4</value>";
        expectedXml += "<value key=\"10\">43</value><value key=\"50\">123.6</value></trial>";
        expectedXml += "<trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
        expectedXml += "</trial></aging></column></agings>"; 

        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }    
}

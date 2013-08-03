package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.DatasetSeasons;

public class XmlDatasetSeasonsReaderTest {

    @Test
    public void testXML() throws SAXException, IOException
    {    
    	String xml = "<seasons>\n";
    	xml += "<column name=\"test\"><season position=\"0.5\"><trial><value key=\"10\">43</value>";
    	xml += "<value key=\"50\">123.6</value></trial></season></column>\n<column name=\"test2\">";
    	xml += "<season position=\"0.0\"><trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
    	xml += "</trial></season></column>\n<column name=\"test3\"><season position=\"0.0\"><trial>";
    	xml += "<value key=\"10\">43</value><value key=\"50\">123.6</value></trial></season>";
    	xml += "<season position=\"0.5\"><trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
    	xml += "</trial></season></column></seasons>";
    	
    	XmlDatasetSeasonsReader reader = new XmlDatasetSeasonsReader();
    	DatasetSeasons seasons = reader.read(xml);
    	
    	assertEquals(3, seasons.values().size());
    	assertEquals(1, seasons.get("test").get(0.5f).size());
    	assertEquals(123.6, seasons.get("test").get(0.5f).getTrial(0).get(1).floatValue(), 0.0001);
    	assertTrue(seasons.keySet().contains("test3"));
    }

}

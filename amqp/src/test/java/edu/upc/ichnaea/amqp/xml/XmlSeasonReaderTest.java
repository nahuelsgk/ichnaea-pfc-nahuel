package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Season;;

public class XmlSeasonReaderTest {

    @Test
    public void testXML() throws SAXException, IOException
    {    
    	String xml = "<season><trial><value key=\"0\">4.4</value><value key=\"10\">40</value>";
        xml += "<value key=\"50\">120.6</value></trial><trial><value key=\"0\">5.4</value>";
        xml += "<value key=\"10\">43</value><value key=\"50\">123.6</value></trial>";
        xml += "<trial><value key=\"10\">43</value><value key=\"50\">123.6</value></trial></season>";        
    	
    	Season season = new XmlSeasonReader().read(xml);
    	
    	assertEquals(3, season.getTrials().size());
    	assertEquals("50", season.getTrial(0).get(2).stringKey());
    	assertEquals("120.6", season.getTrial(0).get(2).stringValue());
    	
    	assertEquals(10, season.getTrial(1).get(1).intKey());
    	assertEquals(43, season.getTrial(1).get(1).intValue());
    	
    	assertEquals(50, season.getTrial(2).get(1).floatKey(), 0.00001);
    	assertEquals(123.6, season.getTrial(2).get(1).floatValue(), 0.00001);    	
    }

}

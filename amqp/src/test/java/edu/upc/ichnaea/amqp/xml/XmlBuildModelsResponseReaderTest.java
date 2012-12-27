package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import javax.mail.MessagingException;
import javax.xml.parsers.ParserConfigurationException;

import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModelsResponse;

import static org.junit.Assert.*;

public class XmlBuildModelsResponseReaderTest {

    @Test
    public void testXML() throws ParserConfigurationException, SAXException, IOException, MessagingException
    {
    	XmlBuildModelsResponseReader reader = new XmlBuildModelsResponseReader();
    	String data = "";
    	BuildModelsResponse resp = reader.read(data);
    	
    	assertEquals(455, resp.getId());
    }
}

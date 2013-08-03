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
    	String data = "--frontier\n";
    	data += "Content-Type: text/xml\n\n";
    	data += "<response end=\"2012-12-28T22:00:00.000+0100\" id=\"455\" progress=\"1.0\" ";
    	data += "start=\"2012-12-27T08:00:00.000+0100\" type=\"build_models\"/>\n";
    	data += "--frontier\n";
    	data += "Content-Type: application/zip\n";
    	data += "Content-Transfer-Encoding: base64\n\n";    	
    	data += "cGFjbw==\n";
    	data += "--frontier--\n";
    	
    	BuildModelsResponse resp = reader.read(data);
    	
    	assertEquals("455", resp.getId());
    	
    	assertEquals("paco", new String(resp.getData()));
    }
}

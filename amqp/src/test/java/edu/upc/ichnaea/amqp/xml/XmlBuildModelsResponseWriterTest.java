package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;

import javax.mail.MessagingException;
import javax.mail.internet.MimeMultipart;
import javax.xml.parsers.ParserConfigurationException;

import org.custommonkey.xmlunit.Diff;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.mail.ByteArrayDataSource;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;

import static org.junit.Assert.*;

public class XmlBuildModelsResponseWriterTest {

    @Test
    public void testXML() throws ParserConfigurationException, SAXException, IOException, MessagingException, ParseException
    {
    	int id = 455;
    	Calendar start = Calendar.getInstance();
    	Calendar end = Calendar.getInstance();
    	
    	SimpleDateFormat f = new SimpleDateFormat("dd-MM-yyyy hh:mm");
    	start.setTime(f.parse("27-12-2012 8:00"));
    	end.setTime(f.parse("28-12-2012 22:00"));
    	
    	byte[] data = new String("paco").getBytes();
    	BuildModelsResponse resp = new BuildModelsResponse(id, start, end, data);
    	
    	String rdata = new XmlBuildModelsResponseWriter().build(resp).toString();

    	MimeMultipart mp = new MimeMultipart(new ByteArrayDataSource(rdata.getBytes()));
    	
    	assertEquals(2, mp.getCount());
    	
    	Object obj = mp.getBodyPart(0).getContent();
    	
    	assertEquals(String.class, obj.getClass());
    	
    	String xml = (String) obj;
        String expectedXml = "<response end=\"2012-12-28T22:00:00.000+0100\" id=\"455\" progress=\"0.0\" ";
        expectedXml += "start=\"2012-12-27T08:00:00.000+0100\" type=\"build_models\"/>";
        
        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }
}

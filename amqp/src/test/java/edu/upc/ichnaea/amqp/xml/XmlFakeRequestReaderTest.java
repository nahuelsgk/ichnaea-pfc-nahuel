package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;

import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.FakeRequest;

public class XmlFakeRequestReaderTest {


    @Test
    public void testXML() throws SAXException, IOException {
        String xml = "<request id=\"432\" type=\"fake\" duration=\"10\" interval=\"1\" />";

        FakeRequest req = new XmlFakeRequestReader()
                .read(xml);
        assertEquals(10, req.getDuration(), 0.000001);
        assertEquals(1, req.getInterval(), 0.000001);
    }

}

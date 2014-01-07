package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Aging;

;

public class XmlAgingReaderTest {

    @Test
    public void testXML() throws SAXException, IOException {
        String xml = "<aging><trial><value key=\"0\">4.4</value><value key=\"10\">40</value>";
        xml += "<value key=\"50\">120.6</value></trial><trial><value key=\"0\">5.4</value>";
        xml += "<value key=\"10\">43</value><value key=\"50\">123.6</value></trial>";
        xml += "<trial><value key=\"10\">43</value><value key=\"50\">123.6</value></trial></aging>";

        Aging aging = new XmlAgingReader().read(xml);

        assertEquals(3, aging.getTrials().size());
        assertEquals("50", aging.getTrial(0).get(2).stringKey());
        assertEquals("120.6", aging.getTrial(0).get(2).stringValue());

        assertEquals(10, aging.getTrial(1).get(1).intKey());
        assertEquals(43, aging.getTrial(1).get(1).intValue());

        assertEquals(50, aging.getTrial(2).get(1).floatKey(), 0.00001);
        assertEquals(123.6, aging.getTrial(2).get(1).floatValue(), 0.00001);
    }

}

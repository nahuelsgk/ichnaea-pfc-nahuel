package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.DatasetAging;

public class XmlDatasetAgingReaderTest {

    @Test
    public void testXML() throws SAXException, IOException {
        String xml = "<agings>\n";
        xml += "<column name=\"test\"><aging position=\"0.5\"><trial><value key=\"10\">43</value>";
        xml += "<value key=\"50\">123.6</value></trial></aging></column>\n<column name=\"test2\">";
        xml += "<aging position=\"0.0\"><trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
        xml += "</trial></aging></column>\n<column name=\"test3\"><aging position=\"0.0\"><trial>";
        xml += "<value key=\"10\">43</value><value key=\"50\">123.6</value></trial></aging>";
        xml += "<aging position=\"0.5\"><trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
        xml += "</trial></aging></column></agings>";

        XmlDatasetAgingReader reader = new XmlDatasetAgingReader();
        DatasetAging agings = reader.read(xml);

        assertEquals(3, agings.values().size());
        assertEquals(1, agings.get("test").get(0.5f).size());
        assertEquals(123.6, agings.get("test").get(0.5f).getTrial(0).get(1)
                .floatValue(), 0.0001);
        assertTrue(agings.keySet().contains("test3"));
    }

}

package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;

import org.custommonkey.xmlunit.Diff;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Aging;
import edu.upc.ichnaea.amqp.model.AgingTrial;

import static org.junit.Assert.*;

public class XmlAgingWriterTest {

    @Test
    public void testEmptyXML() throws ParserConfigurationException,
            SAXException, IOException {
        Aging aging = new Aging();

        String xml = new XmlAgingWriter().build(aging).toString();
        String expectedXml = "<aging></aging>";

        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }

    @Test
    public void testXML() throws ParserConfigurationException, SAXException,
            IOException {
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

        String xml = new XmlAgingWriter().build(aging).toString();
        String expectedXml = "<aging><trial><value key=\"0\">4.4</value><value key=\"10\">40</value>";
        expectedXml += "<value key=\"50\">120.6</value></trial><trial><value key=\"0\">5.4</value>";
        expectedXml += "<value key=\"10\">43</value><value key=\"50\">123.6</value></trial>";
        expectedXml += "<trial><value key=\"10\">43</value><value key=\"50\">123.6</value></trial></aging>";

        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }
}

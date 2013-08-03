package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;

import org.custommonkey.xmlunit.Diff;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.DatasetSeasons;
import edu.upc.ichnaea.amqp.model.DatasetSeasonsColumn;
import edu.upc.ichnaea.amqp.model.Season;
import edu.upc.ichnaea.amqp.model.SeasonTrial;

import static org.junit.Assert.*;

public class XmlDatasetSeasonsWriterTest {

    @Test
    public void testEmptyXML() throws ParserConfigurationException, SAXException, IOException
    {
    	DatasetSeasons seasons = new DatasetSeasons();
    	
    	String xml = new XmlDatasetSeasonsWriter().build(seasons).toString();
        String expectedXml = "<seasons></seasons>";
        
        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }
    
    @Test
    public void testXML() throws ParserConfigurationException, SAXException, IOException
    {
    	DatasetSeasons seasons = new DatasetSeasons();
    	DatasetSeasonsColumn column = new DatasetSeasonsColumn();
    	Season season = new Season();
    	SeasonTrial trial = new SeasonTrial();
    	trial.add("0", "4.4");
    	trial.add("10", "40");
    	trial.add("50", "120.6");
    	season.addTrial(trial);
    	trial = new SeasonTrial();
    	trial.add("0", "5.4");
    	trial.add("10", "43");
    	trial.add("50", "123.6");
    	season.addTrial(trial);
    	trial = new SeasonTrial();
    	trial.add("10", "43");
    	trial.add("50", "123.6");
    	season.addTrial(trial);
    	column.put(0.5f, season);
    	seasons.put("test", column);
    	
    	column = new DatasetSeasonsColumn();
    	season = new Season();
    	season.addTrial(trial);
    	column.put(0.0f, season);
    	column.put(0.2f, season);
    	seasons.put("test2", column);
    	
    	String xml = new XmlDatasetSeasonsWriter().build(seasons).toString();
    	String expectedXml = "<seasons><column name=\"test\"><season position=\"0.5\"><trial>";
    	expectedXml += "<value key=\"0\">4.4</value><value key=\"10\">40</value>";
    	expectedXml += "<value key=\"50\">120.6</value></trial><trial><value key=\"0\">5.4</value>";
    	expectedXml += "<value key=\"10\">43</value><value key=\"50\">123.6</value></trial>";
    	expectedXml += "<trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
    	expectedXml += "</trial></season></column><column name=\"test2\"><season position=\"0.0\">";
    	expectedXml += "<trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
    	expectedXml += "</trial></season><season position=\"0.2\">";
    	expectedXml += "<trial><value key=\"10\">43</value><value key=\"50\">123.6</value>";
    	expectedXml += "</trial></season></column></seasons>"; 
        
        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }    
}

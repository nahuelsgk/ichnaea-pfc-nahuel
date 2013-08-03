package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;

import org.custommonkey.xmlunit.Diff;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;
import edu.upc.ichnaea.amqp.model.DatasetSeasons;
import edu.upc.ichnaea.amqp.model.DatasetSeasonsColumn;
import edu.upc.ichnaea.amqp.model.Season;
import edu.upc.ichnaea.amqp.model.SeasonTrial;


public class XmlBuildModelsRequestWriterTest {

    @Test
    public void testXML() throws ParserConfigurationException, SAXException, IOException
    {    
    	Dataset dataset = new Dataset();
        DatasetColumn column = new DatasetColumn("test");
        column.add(1f);
        column.add(2f);
        column.add(3f);
        dataset.add(column);
        
        DatasetColumn column2 = new DatasetColumn("test2");
        column2.add(4f);
        column2.add(5f);
        column2.add(6f);
        dataset.add(column2);
        
        DatasetSeasons seasons = new DatasetSeasons();
        
        String id = "455";
        BuildModelsRequest model = new BuildModelsRequest(id, dataset, seasons);
        
        String xml = new XmlBuildModelsRequestWriter().build(model).toString();
        
        String expectedXml = "<request id=\"455\" type=\"build_models\"><dataset><column name=\"test\">" +
        		"<value>1.0</value><value>2.0</value><value>3.0</value></column><column name=\"test2\">" +
        		"<value>4.0</value><value>5.0</value><value>6.0</value></column></dataset></request>";
        
        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }
    
    @Test
    public void testSeasonsXML() throws ParserConfigurationException, SAXException, IOException
    {    
    	Dataset dataset = new Dataset();
        DatasetColumn column = new DatasetColumn("test");
        column.add(1f);
        column.add(2f);
        column.add(3f);
        dataset.add(column);
        
        DatasetSeasons seasons = new DatasetSeasons();
    	DatasetSeasonsColumn seasonColumn = new DatasetSeasonsColumn();
    	Season season = new Season();
    	SeasonTrial trial = new SeasonTrial();
    	trial.add("0", "4.4");
    	trial.add("10", "40");
    	trial.add("50", "120.6");
    	season.addTrial(trial);
    	seasonColumn.put(0.5f, season);
    	seasons.put("test", seasonColumn);
        
        String id = "455";
        BuildModelsRequest model = new BuildModelsRequest(id, dataset, seasons);
        
        String xml = new XmlBuildModelsRequestWriter().build(model).toString();
        
        String expectedXml = "<request id=\"455\" type=\"build_models\"><dataset><column name=\"test\">" +
        		"<value>1.0</value><value>2.0</value><value>3.0</value></column></dataset><seasons>" +
        		"<column name=\"test\"><season position=\"0.5\"><trial>" +
    			"<value key=\"0\">4.4</value><value key=\"10\">40</value>" +
    			"<value key=\"50\">120.6</value></trial></season></column></seasons></request>";
        
        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }    
}

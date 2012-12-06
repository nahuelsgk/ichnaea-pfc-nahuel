package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;
import java.io.StringReader;
import java.util.Set;

import org.junit.Test;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class BuildModelsHandlerTest {

    @Test
    public void testXML() throws SAXException, IOException
    {    
    	String xml = "<message type=\"build_models\" season=\"winter\"><dataset>\n";
    	xml += "<column name=\"test\"><value>1.5</value><value>2</value><value>3</value></column>\n";
    	xml += "<column name=\"test2\"><value>3</value><value>4</value></column>\n";
    	xml += "<column name=\"test3\"><value>5</value><value>6</value><value>7</value></column>\n";    	
    	xml += "</dataset></message>";
    	
    	BuildModelsHandler handler = new BuildModelsHandler();
    	XMLReader parser = XMLReaderFactory.createXMLReader();
    	parser.setContentHandler(handler);
    	parser.parse(new InputSource(new StringReader(xml)));
    	
    	BuildModels message = handler.getMessage();
    	
    	assertTrue(BuildModels.Season.Winter == message.getSeason());
    	
    	Dataset dataset = message.getDataset();
    	
    	Set<String> names = dataset.getColumnNames();
    	assertTrue(names.contains("test"));
    	assertEquals(3, names.size());
    	
    	DatasetColumn column = dataset.get("test");
    	
    	assertTrue(1.5 == column.get(0).floatValue());
    	
    	column = dataset.get("test2");
    	assertEquals(2, column.size());
    }

}

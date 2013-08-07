package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;
import java.util.Collection;

import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModelsFakeRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class XmlBuildModelsRequestReaderTest {

    @Test
    public void testXML() throws SAXException, IOException
    {    
    	String xml = "<request id=\"432\" type=\"build_models\"><dataset>\n";
    	xml += "<column name=\"test\"><value>1.5</value><value>2</value><value>3</value></column>\n";
    	xml += "<column name=\"test2\"><value>3</value><value>4</value></column>\n";
    	xml += "<column name=\"test3\"><value>5</value><value>6</value><value>7</value></column>\n";    	
    	xml += "</dataset></request>";
    	
    	BuildModelsRequest message = new XmlBuildModelsRequestReader().read(xml);
    	
    	Dataset dataset = message.getDataset();
    	
    	Collection<String> names = dataset.columnNames();
    	assertTrue(names.contains("test"));
    	assertEquals(3, names.size());
    	
    	DatasetColumn column = dataset.get("test");
    	
    	assertTrue(1.5 == column.get(0).floatValue());
    	
    	column = dataset.get("test2");
    	assertEquals(2, column.size());
    }
    
    @Test
    public void testFakeXML() throws SAXException, IOException
    {     
    	String xml = "<request id=\"432\" type=\"build_models\" fake=\"10:1\" />";
    	
    	BuildModelsRequest message = new XmlBuildModelsRequestReader().read(xml);
    	assertTrue(message instanceof BuildModelsFakeRequest);
    	BuildModelsFakeRequest fake = (BuildModelsFakeRequest) message;
    	assertEquals(10, fake.getDuration(), 0.000001);
    	assertEquals(1, fake.getInterval(), 0.000001);
    }

}

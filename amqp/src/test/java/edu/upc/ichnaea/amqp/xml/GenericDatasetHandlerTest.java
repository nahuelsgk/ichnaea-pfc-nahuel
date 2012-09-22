package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;
import java.io.StringReader;
import java.util.Set;
import java.util.List;

import org.junit.Test;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import edu.upc.ichnaea.amqp.model.GenericDataset;

class IntegerDatasetHandler extends GenericDatasetHandler<Integer, GenericDataset<Integer>>
{
	@Override
	GenericDataset<Integer> createDataset() {
		return new GenericDataset<Integer>();
	}

	@Override
	protected Integer stringToValue(String text) {
		return Integer.valueOf(text, 10);
	}
}

public class GenericDatasetHandlerTest {

    @Test
    public void testXML() throws SAXException, IOException
    {    
    	String xml = "<dataset>\n";
    	xml += "<column name=\"test\"><value>1</value><value>2</value><value>3</value></column>\n";
    	xml += "<column name=\"test2\"><value>3</value><value>4</value></column>\n";
    	xml += "<column name=\"test3\"><value>5</value><value>6</value><value>7</value></column>\n";    	
    	xml += "</dataset>";
    	
    	IntegerDatasetHandler handler = new IntegerDatasetHandler();
    	XMLReader parser = XMLReaderFactory.createXMLReader();
    	parser.setContentHandler(handler);
    	parser.parse(new InputSource(new StringReader(xml)));
    	
    	GenericDataset<Integer> set = handler.getDataset();
    	
    	Set<String> names = set.getColumnNames();
    	assertTrue(names.contains("test"));
    	assertEquals(3, names.size());
    	
    	List<Integer> column = set.getColumn("test");
    	
    	assertEquals(1, column.get(0).intValue());
    	assertEquals(2, column.get(1).intValue());    	
    	assertEquals(3, column.get(2).intValue());
    	
    	column = set.getColumn("test2");
    	assertEquals(3, column.get(0).intValue());
    	assertEquals(4, column.get(1).intValue());
    	assertEquals(2, column.size());

    }

}

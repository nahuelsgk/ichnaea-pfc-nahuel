package edu.upc.ichnaea.amqp.csv;

import static org.junit.Assert.*;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import org.junit.Test;

import edu.upc.ichnaea.amqp.model.Dataset;

public class DatasetReaderTest {

    @Test
    public void testFile() throws IOException
    {
    	
    	InputStream in = getClass().getClassLoader().getResourceAsStream("test.csv");
    	DatasetReader reader = new DatasetReader();
    	reader.setSeparator(';');
    	Dataset dataset = reader.read(new InputStreamReader(in));
    	
    	assertEquals(27, dataset.columnNames().size());
    	
    	dataset.get("RYC2056");
    }
}

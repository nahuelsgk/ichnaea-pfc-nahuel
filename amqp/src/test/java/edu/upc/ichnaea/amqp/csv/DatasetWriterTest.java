package edu.upc.ichnaea.amqp.csv;

import static org.junit.Assert.*;

import java.io.IOException;
import java.io.StringWriter;

import org.junit.Test;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class DatasetWriterTest {

    @Test
    public void testCSV() throws IOException
    {
        Dataset dataset = new Dataset();
        DatasetColumn column = new DatasetColumn("test");
        column.add(1);
        column.add(2);
        column.add(3);
        dataset.add(column);

        column = new DatasetColumn("test2");
        column.add(4);
        column.add(5);
        dataset.add(column);
        
        column = new DatasetColumn("test3");
        column.add(6);
        column.add(7);
        column.add(8);    	
        dataset.add(column);
      
        StringWriter strWriter = new StringWriter();
        DatasetWriter writer = new DatasetWriter(strWriter);
        writer.setQuote('\'');
        writer.write(dataset).close();
        
        String expectedCsv = "'test';'test2';'test3'\n" +
        			"'1';'4';'6'\n" +
        			"'2';'5';'7'\n" +
        			"'3';;'8'\n";
        assertEquals(expectedCsv, strWriter.toString());
    }
}

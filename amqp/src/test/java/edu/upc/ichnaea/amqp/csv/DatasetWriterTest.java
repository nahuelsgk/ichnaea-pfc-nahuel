package edu.upc.ichnaea.amqp.csv;

import org.junit.Test;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class DatasetWriterTest {

    @Test
    public void testCSV()
    {
        Dataset set = new Dataset();
        DatasetColumn column = new DatasetColumn("test");
        column.add(1);
        column.add(2);
        column.add(3);
        set.add(column);

        column = new DatasetColumn("test2");
        column.add(4);
        column.add(5);
        set.add(column);
        
        column = new DatasetColumn("test3");
        column.add(6);
        column.add(7);
        column.add(8);    	
        set.add(column);
      
    }
}

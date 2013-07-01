package edu.upc.ichnaea.amqp.model;

import org.junit.*;
import static org.junit.Assert.*;

public class DatasetTest {

    @Test
    public void testReadingRows()
    {
        Dataset set = new Dataset();
        DatasetColumn column = new DatasetColumn("test");
        column.add(1);
        column.add(2);
        column.add(3);
        set.add(column);
        DatasetRow row = set.getRow(0);
        
        assertEquals(1, row.get("test").intValue());
        assertEquals(1, row.size());
        
        column = new DatasetColumn("test2");
        
        column.add(4);
        column.add(5);
        
        set.add(column);
        
        row = set.getRow(0);
        
        assertEquals(1, row.get("test").intValue());
        assertEquals(4, row.get("test2").intValue());        
        assertEquals(2, row.size()); 
        
        row = set.getRow(2);
        
        assertEquals(3, row.get("test").intValue());
        assertEquals(1, row.size());
        
        column = new DatasetColumn("test3");
        
        column.add(6);
        column.add(7);
        column.add(8);
        
        set.add(column);        

        row = set.getRow(2);
        
        assertEquals(3, row.get("test").intValue());
        assertEquals(8, row.get("test3").intValue());        
        assertEquals(2, row.size());
        
    }

}
    

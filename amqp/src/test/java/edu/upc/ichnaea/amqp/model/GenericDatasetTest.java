package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import org.junit.*;
import static org.junit.Assert.*;

public class GenericDatasetTest {

    @Test
    public void testSettingValues()
    {
        GenericDataset<Integer> set = new GenericDataset<Integer>();
        List<Integer> column = new ArrayList<Integer>();
        column.add(1);
        column.add(2);
        column.add(3);
        set.setColumn("test", column);
        Map<String,Integer> row = set.getRow(0);
        
        assertEquals(1, row.get("test").intValue());
        assertEquals(1, row.size());
        
        column = new ArrayList<Integer>();
        
        column.add(4);
        column.add(5);
        
        set.setColumn("test2", column);
        
        row = set.getRow(0);
        
        assertEquals(1, row.get("test").intValue());
        assertEquals(4, row.get("test2").intValue());        
        assertEquals(2, row.size()); 
        
        row = set.getRow(2);
        
        assertEquals(3, row.get("test").intValue());
        assertEquals(1, row.size());
        
        column = new ArrayList<Integer>();
        
        column.add(6);
        column.add(7);
        column.add(8);
        
        set.setColumn("test3", column);        

        row = set.getRow(2);
        
        assertEquals(3, row.get("test").intValue());
        assertEquals(8, row.get("test3").intValue());        
        assertEquals(2, row.size());
    }    
    
    @Test
    public void testCSV()
    {
        GenericDataset<Integer> set = new GenericDataset<Integer>();
        List<Integer> column = new ArrayList<Integer>();
        column.add(1);
        column.add(2);
        column.add(3);
        set.setColumn("test", column);

        column = new ArrayList<Integer>();
        column.add(4);
        column.add(5);
        set.setColumn("test2", column);
        
        column = new ArrayList<Integer>();
        column.add(6);
        column.add(7);
        column.add(8);    	
        set.setColumn("test3", column);
        
        String csv = set.toCsv(".", ";");
        assertEquals("test;test2;test3\n1;4;6\n2;5;7\n3;;8", csv);
    }
}

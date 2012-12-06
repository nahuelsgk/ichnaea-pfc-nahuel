package edu.upc.ichnaea.amqp.csv;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import edu.upc.ichnaea.amqp.model.Dataset;

public class CsvDatasetReader extends CsvReader {
	
	public Dataset read(Reader reader) throws IOException
	{
		return Dataset.create(readCols(reader));
	}
	
	protected HashMap<String, List<String>> readCols(Reader reader) throws IOException
	{
	    List<String[]> rows = readRows(reader);
		HashMap<String, List<String>> cols = new HashMap<String, List<String>>(); 	    
	    
	    if(rows.size()<=1)
	    {
	    	throw new IOException("First row needs to have the data type names.");
	    }
	    
	    String[] names = rows.get(0);
	    
	    for(int i=0; i<names.length; i++)
	    {
	    	cols.put(names[i], new ArrayList<String>());
	    }
		
	    for(int j=1;j<rows.size(); j++)
	    {
	    	String[] row = rows.get(j);
	    	for(int k=0;k<row.length; k++)
	    	{
	    		cols.get(names[k]).add(row[k]);
	    	}
	    }
	    
		return cols;
	}

}

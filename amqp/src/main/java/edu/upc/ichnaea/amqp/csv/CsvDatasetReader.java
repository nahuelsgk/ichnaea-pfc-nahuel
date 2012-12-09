package edu.upc.ichnaea.amqp.csv;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.Collection;
import java.util.List;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class CsvDatasetReader extends CsvReader {
	
	public Dataset read(Reader reader) throws IOException
	{
		return new Dataset(readCols(reader));
	}
	
	protected Collection<DatasetColumn> readCols(Reader reader) throws IOException
	{
	    List<String[]> rows = readRows(reader);
		List<DatasetColumn> cols = new ArrayList<DatasetColumn>(); 	    
	    
	    if(rows.size()<=1)
	    {
	    	throw new IOException("First row needs to have the data type names.");
	    }
	    
	    String[] names = rows.get(0);
	    
	    for(int i=0; i<names.length; i++)
	    {
	    	cols.add(new DatasetColumn(names[i]));
	    }
		
	    for(int j=1;j<rows.size(); j++)
	    {
	    	String[] row = rows.get(j);
	    	int len = Math.min(row.length, names.length);
	    	for(int k=0;k<len; k++)
	    	{
	    		cols.get(k).add(row[k]);
	    	}
	    }
	    
		return cols;
	}

}

package edu.upc.ichnaea.amqp.csv;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import edu.upc.ichnaea.amqp.model.GenericDataset;

abstract public class GenericDatasetReader<F, D extends GenericDataset<F>> extends CSVReader {
	
	protected abstract F stringToValue(String text);
	protected abstract D createDataset(HashMap<String, List<F>> cols);
	
	public D read(Reader reader) throws IOException
	{
		return createDataset(readCols(reader));
	}
	
	protected HashMap<String, List<F>> readCols(Reader reader) throws IOException
	{
	    List<String[]> rows = readRows(reader);
		HashMap<String, List<F>> cols = new HashMap<String, List<F>>(); 	    
	    
	    if(rows.size()<=1)
	    {
	    	throw new IOException("First row needs to have the data type names.");
	    }
	    
	    String[] names = rows.get(0);
	    
	    for(int i=0; i<names.length; i++)
	    {
	    	cols.put(names[i], new ArrayList<F>());
	    }
		
	    for(int j=1;j<rows.size(); j++)
	    {
	    	String[] row = rows.get(j);
	    	for(int k=0;k<row.length; k++)
	    	{
	    		List<F> col = cols.get(k);
	    		col.set(j, stringToValue(row[j]));
	    	}
	    }
	    
		return cols;
	}

}

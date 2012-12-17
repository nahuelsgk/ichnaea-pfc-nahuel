package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;

public class GenericDataset<F> {

	protected Map<String, List<F>> mColumns;
	
	public void setColumn(String name, List<F> col)
	{
		mColumns.put(name, col);
	}
	
	public List<F> getColumn(String name)
	{
		return mColumns.get(name);
	}
	
	public Map<String, List<F>> getColumns()
	{
		return mColumns;
	}
	
	public Set<String> getColumnNames()
	{
		return mColumns.keySet();
	}
	
	public List<Map<String, F>> getRows()
	{
		List<Map<String, F>> rows = new ArrayList<Map<String, F>>();
		int c = 0;
		while(true){
			Map<String, F> row = getRow(c++);
			if(row.size() == 0)
			{
				break;
			}
			rows.add(row);
		}		
		return rows;
	}
	
	public Map<String, F> getRow(int i)
	{
		Map<String, F> row = new HashMap<String, F>();
		for(String name: getColumnNames())
		{
			List<F> column = getColumn(name);
			if(column.size()>i){
				row.put(name, column.get(i));
			}
		}
		return row;		
	}
	
	public String getCsv(String decimal, String delimiter)
	{
		Set<String> names = getColumnNames();
		String csv = join(names, delimiter)+"\n";
		for(Map<String, F> row: getRows())
		{
			List<F> values = new ArrayList<F>();
			for(String name: names){
				values.add(row.get(name));
			}
			csv += join(values, delimiter)+"\n";
		}
		return csv;
	}
	
	 static String join(Collection<?> s, String delimiter) {
	     StringBuilder builder = new StringBuilder();
	     Iterator<?> iter = s.iterator();
	     while (iter.hasNext()) {
	         builder.append(iter.next());
	         if (!iter.hasNext()) {
	           break;                  
	         }
	         builder.append(delimiter);
	     }
	     return builder.toString();
	 }	
}
package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;

public class Dataset implements Iterable<DatasetColumn> {

	protected Set<DatasetColumn> mColumns;
	
	public Dataset() {
		this(new HashSet<DatasetColumn>());
	}
	
	public Dataset(Set<DatasetColumn> cols) {
		mColumns = cols;
	}
	
	public static Dataset create(Map<String, List<String>> cols) {
		Dataset dataset = new Dataset();
		for(String name: cols.keySet()) {
			dataset.add(DatasetColumn.create(name, cols.get(name)));
		}
		return dataset;
	}	
	
	public void add(DatasetColumn col)
	{
		mColumns.add(col);
	}
	
	public DatasetColumn get(String name) {
		for(DatasetColumn col : mColumns) {
			if(name.equals(col.getName())) {
				return col;
			}
		}
		return null;
	}
	
	public Set<DatasetColumn> columns() {
		return mColumns;
	}
	
	public Set<String> columnNames() {
		Set<String> names = new HashSet<String>();
		for(DatasetColumn col: mColumns) {
			names.add(col.getName());
		}
		return names;
	}
	
	public List<DatasetRow> rows() {
		List<DatasetRow> rows = new ArrayList<DatasetRow>();
		int c = 0;
		while(true){
			DatasetRow row = getRow(c++);
			if(row.size() == 0) {
				break;
			}
			rows.add(row);
		}		
		return rows;
	}
	
	public DatasetRow getRow(int i) {
		DatasetRow row = new DatasetRow();
		for(DatasetColumn col: this) {
			if(col.size()>i) {
				row.put(col.getName(), col.get(i));
			}
		}
		return row;		
	}

	@Override
	public Iterator<DatasetColumn> iterator() {
		return mColumns.iterator();
	}
}
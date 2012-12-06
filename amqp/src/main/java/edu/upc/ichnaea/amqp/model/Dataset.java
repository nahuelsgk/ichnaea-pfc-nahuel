package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.SortedSet;
import java.util.TreeSet;

public class Dataset implements Iterable<DatasetColumn>, Comparable<Dataset> {

	protected Collection<DatasetColumn> mColumns;
	
	public Dataset() {
		this(new TreeSet<DatasetColumn>());
	}
	
	public Dataset(Collection<DatasetColumn> cols) {
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
	
	public Collection<DatasetColumn> columns() {
		return mColumns;
	}
	
	public SortedSet<String> columnNames() {
		SortedSet<String> names = new TreeSet<String>();
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

	@Override
	public int compareTo(Dataset o) {
		int diff = columns().size() - o.columns().size();
		if(diff != 0) {
			return diff;
		}
		for(String name: columnNames()) {
			DatasetColumn col = o.get(name);
			if(col == null) {
				diff = 1;
			} else {
				diff = get(name).compareTo(col);
			}
		}
		return diff;
	}
}
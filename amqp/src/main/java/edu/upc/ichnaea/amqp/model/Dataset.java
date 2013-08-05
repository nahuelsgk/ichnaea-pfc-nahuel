package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;
import java.util.List;

public class Dataset implements Iterable<DatasetColumn>, Comparable<Dataset> {

	protected Collection<DatasetColumn> mColumns;
	
	public Dataset() {
		this(new ArrayList<DatasetColumn>());
	}
	
	public Dataset(Collection<DatasetColumn> cols) {
		mColumns = cols;
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
	
	public Collection<String> columnNames() {
		List<String> names = new ArrayList<String>();	
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
	
	public boolean isEmpty() {
		for(DatasetColumn col : mColumns) {
			if(!col.isEmpty()) {
				return false;
			}
		}
		return true;
	}	
}
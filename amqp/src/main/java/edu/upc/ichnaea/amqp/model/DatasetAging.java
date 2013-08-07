package edu.upc.ichnaea.amqp.model;

import java.util.Collection;
import java.util.HashMap;
import java.util.Map;
import java.util.Set;

public class DatasetAging implements Comparable<DatasetAging> {

	protected Map<String, DatasetAgingColumn> mColumns;
	
	public DatasetAging() {
		this(new HashMap<String, DatasetAgingColumn>());
	}
	
	public DatasetAging(Map<String, DatasetAgingColumn> columns) {
		mColumns = columns;
	}
	
	public DatasetAgingColumn get(String column) {
		return mColumns.get(column);
	}
	
	public void put(String name, DatasetAgingColumn column) {
		mColumns.put(name, column);
	}
	
	public int size() {
		return mColumns.size();
	}
	
	public Set<String> keySet() {
		return mColumns.keySet();
	}
	
	public Collection<DatasetAgingColumn> values() {
		return mColumns.values();
	}

	@Override
	public int compareTo(DatasetAging o) {
		int c = mColumns.size()-o.mColumns.size();
		if(c == 0) {
			for(String i : mColumns.keySet()) {
				c = mColumns.get(i).compareTo(o.mColumns.get(i));
				if(c != 0) {
					return c;
				}
			}
			return 0;
		} else {
			return c;
		}
	}
	
	public boolean isEmpty() {
		for(DatasetAgingColumn col : mColumns.values()) {
			if(!col.isEmpty()) {
				return false;
			}
		}
		return true;
	}
}

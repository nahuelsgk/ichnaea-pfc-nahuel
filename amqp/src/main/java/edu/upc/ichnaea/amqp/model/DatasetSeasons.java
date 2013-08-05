package edu.upc.ichnaea.amqp.model;

import java.util.Collection;
import java.util.HashMap;
import java.util.Map;
import java.util.Set;

public class DatasetSeasons implements Comparable<DatasetSeasons> {

	protected Map<String, DatasetSeasonsColumn> mColumns;
	
	public DatasetSeasons() {
		this(new HashMap<String, DatasetSeasonsColumn>());
	}
	
	public DatasetSeasons(Map<String, DatasetSeasonsColumn> columns) {
		mColumns = columns;
	}
	
	public DatasetSeasonsColumn get(String column) {
		return mColumns.get(column);
	}
	
	public void put(String name, DatasetSeasonsColumn column) {
		mColumns.put(name, column);
	}
	
	public int size() {
		return mColumns.size();
	}
	
	public Set<String> keySet() {
		return mColumns.keySet();
	}
	
	public Collection<DatasetSeasonsColumn> values() {
		return mColumns.values();
	}

	@Override
	public int compareTo(DatasetSeasons o) {
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
		for(DatasetSeasonsColumn col : mColumns.values()) {
			if(!col.isEmpty()) {
				return false;
			}
		}
		return true;
	}
}

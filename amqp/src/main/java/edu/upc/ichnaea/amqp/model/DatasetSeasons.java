package edu.upc.ichnaea.amqp.model;

import java.util.Collection;
import java.util.HashMap;
import java.util.Map;
import java.util.Set;

public class DatasetSeasons implements Comparable<DatasetSeasons> {

	protected Map<String, DatasetColumnSeasons> mColumns;
	
	public DatasetSeasons() {
		this(new HashMap<String, DatasetColumnSeasons>());
	}
	
	public DatasetSeasons(Map<String, DatasetColumnSeasons> columns) {
		mColumns = columns;
	}
	
	public DatasetColumnSeasons get(String column) {
		return mColumns.get(column);
	}
	
	public void put(String column, DatasetColumnSeasons seasons) {
		mColumns.put(column, seasons);
	}
	
	public int size() {
		return mColumns.size();
	}
	
	public Set<String> keySet() {
		return mColumns.keySet();
	}
	
	public Collection<DatasetColumnSeasons> values() {
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
}

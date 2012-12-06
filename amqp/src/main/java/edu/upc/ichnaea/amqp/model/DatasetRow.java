package edu.upc.ichnaea.amqp.model;

import java.util.Collection;
import java.util.HashMap;
import java.util.Map;
import java.util.Set;

public class DatasetRow {

	Map<String, DatasetCell> mCells;
	
	DatasetRow() {
		this(new HashMap<String, DatasetCell>());
	}
	
	DatasetRow(Map<String, DatasetCell> cells) {
		mCells = cells;
	}
	
	public void put(String name, DatasetCell cell) {
		mCells.put(name, cell);
	}
	
	public Set<String> columns()
	{
		return mCells.keySet();
	}
	
	public Collection<DatasetCell> cells()
	{
		return mCells.values();
	}
	
	public DatasetCell get(String name) {
		return mCells.get(name);
	}
	
	public int size() {
		return mCells.size();
	}
}

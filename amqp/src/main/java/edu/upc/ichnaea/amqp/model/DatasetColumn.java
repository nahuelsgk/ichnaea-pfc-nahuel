package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

public class DatasetColumn implements Iterable<DatasetCell>, Comparable<DatasetColumn> {

	String mName;
	List<DatasetCell> mCells;

	public DatasetColumn() {
		this(null, new ArrayList<DatasetCell>());
	}
	
	public DatasetColumn(String name) {
		this(name, new ArrayList<DatasetCell>());
	}
	
	public DatasetColumn(String name, List<DatasetCell> cells) {
		mName = name;
		mCells = cells;
	}
	
	public static DatasetColumn create(String name, List<String> cells) {
		DatasetColumn col = new DatasetColumn(name);
		for(String cell : cells)
		{
			col.add(cell);
		}
		return col;
	}
	
	public String getName() {
		return mName;
	}
	
	public void setName(String name) {
		mName = name;
	}
	
	public int size() {
		return mCells.size();
	}
	
	public DatasetCell get(int index) {
		return mCells.get(index);
	}
	
	public void add(DatasetCell cell) {
		mCells.add(cell);
	}
	
	public void add(String cell) {
		add(new DatasetCell(cell));
	}
	
	public void add(int cell) {
		add(new DatasetCell(cell));
	}
	
	public void add(float cell) {
		add(new DatasetCell(cell));
	}

	@Override
	public Iterator<DatasetCell> iterator() {
		return mCells.iterator();
	}

	@Override
	public int compareTo(DatasetColumn o) {
		int diff = size() - o.size();
		if(diff != 0) {
			return diff;
		}
		for(int i=0; i<size(); i++) {
			diff = get(i).compareTo(o.get(i));
			if(diff != 0) {
				break;
			}
		}
		return diff;
	}
	
	public boolean isEmpty() {
		return mCells.isEmpty();
	}	
	
}

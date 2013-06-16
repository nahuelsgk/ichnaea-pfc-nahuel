package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

public class SeasonTrial implements Iterable<SeasonValue>, Comparable<SeasonTrial> {

	protected List<SeasonValue> mValues;
	
	public SeasonTrial() {
		this(new ArrayList<SeasonValue>());
	}
	
	public SeasonTrial(List<SeasonValue> values) {
		mValues = values;
	}

	@Override
	public int compareTo(SeasonTrial o) {
		int c = mValues.size()-o.mValues.size();
		if(c == 0) {
			for(int i=0; i<mValues.size(); i++) {
				c = mValues.get(i).compareTo(o.mValues.get(i));
				if(c != 0) {
					return c;
				}
			}
			return 0;
		} else {
			return c;
		}
	}
	
	public int size() {
		return mValues.size();
	}
	
	public SeasonValue get(int index) {
		return mValues.get(index);
	}
	
	public void add(SeasonValue val) {
		mValues.add(val);
	}
	
	public void add(String key, String val) {
		add(new SeasonValue(key, val));
	}

	@Override
	public Iterator<SeasonValue> iterator() {
		return mValues.iterator();
	}
	
}

package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

public class AgingTrial implements Iterable<AgingValue>, Comparable<AgingTrial> {

	protected List<AgingValue> mValues;
	
	public AgingTrial() {
		this(new ArrayList<AgingValue>());
	}
	
	public AgingTrial(List<AgingValue> values) {
		mValues = values;
	}

	@Override
	public int compareTo(AgingTrial o) {
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
	
	public AgingValue get(int index) {
		return mValues.get(index);
	}
	
	public void add(AgingValue val) {
		mValues.add(val);
	}
	
	public void add(String key, String val) {
		add(new AgingValue(key, val));
	}

	@Override
	public Iterator<AgingValue> iterator() {
		return mValues.iterator();
	}
	
	public boolean isEmpty() {
		return mValues.isEmpty();
	}
	
}

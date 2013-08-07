package edu.upc.ichnaea.amqp.model;

import java.util.Collection;
import java.util.HashMap;
import java.util.Map;
import java.util.Set;

/**
 * Model class that represents all the aging data for a dataset column.
 * Index for an aging is a float that represents the position of the aging
 * inside a timeline (For example Winter=0.0, Summer=0.5).
 * 
 * @author mibero
 */
public class DatasetAgingColumn implements Comparable<DatasetAgingColumn> {

	protected Map<Float, Aging> mAgings;
	
	public DatasetAgingColumn() {
		this(new HashMap<Float, Aging>());
	}
	
	public DatasetAgingColumn(Map<Float, Aging> agings) {
		mAgings = agings;
	}
	
	public void put(float position, Aging aging) {
		mAgings.put(position, aging);
	}
	
	public Aging get(float position) {
		return mAgings.get(position);
	}
	
	public int size() {
		return mAgings.size();
	}
	
	public Set<Float> keySet() {
		return mAgings.keySet();
	}
	
	public Collection<Aging> values() {
		return mAgings.values();
	}

	@Override
	public int compareTo(DatasetAgingColumn o) {
		int c = mAgings.size()-o.mAgings.size();
		if(c == 0) {
			for(float i : mAgings.keySet()) {
				c = mAgings.get(i).compareTo(o.mAgings.get(i));
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
		for(Aging aging : mAgings.values()) {
			if(!aging.isEmpty()) {
				return false;
			}
		}
		return true;
	}
}

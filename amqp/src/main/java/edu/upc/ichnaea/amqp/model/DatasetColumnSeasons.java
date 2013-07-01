package edu.upc.ichnaea.amqp.model;

import java.util.Collection;
import java.util.HashMap;
import java.util.Map;
import java.util.Set;

public class DatasetColumnSeasons implements Comparable<DatasetColumnSeasons> {

	protected Map<Float, Season> mSeasons;
	
	public DatasetColumnSeasons() {
		this(new HashMap<Float, Season>());
	}
	
	public DatasetColumnSeasons(Map<Float, Season> seasons) {
		mSeasons = seasons;
	}
	
	public void put(float position, Season season) {
		mSeasons.put(position, season);
	}
	
	public int size() {
		return mSeasons.size();
	}
	
	public Set<Float> keySet() {
		return mSeasons.keySet();
	}
	
	public Collection<Season> values() {
		return mSeasons.values();
	}

	@Override
	public int compareTo(DatasetColumnSeasons o) {
		int c = mSeasons.size()-o.mSeasons.size();
		if(c == 0) {
			for(float i : mSeasons.keySet()) {
				c = mSeasons.get(i).compareTo(o.mSeasons.get(i));
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

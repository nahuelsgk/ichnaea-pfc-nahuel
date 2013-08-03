package edu.upc.ichnaea.amqp.model;

import java.util.Collection;
import java.util.HashMap;
import java.util.Map;
import java.util.Set;

/**
 * Model class that represents all the season data for a dataset column.
 * Index for a season is a float that represents the position of the season
 * inside a timeline (For example Winter=0.0, Summer=0.5).
 * 
 * @author mibero
 */
public class DatasetSeasonsColumn implements Comparable<DatasetSeasonsColumn> {

	protected Map<Float, Season> mSeasons;
	
	public DatasetSeasonsColumn() {
		this(new HashMap<Float, Season>());
	}
	
	public DatasetSeasonsColumn(Map<Float, Season> seasons) {
		mSeasons = seasons;
	}
	
	public void put(float position, Season season) {
		mSeasons.put(position, season);
	}
	
	public Season get(float position) {
		return mSeasons.get(position);
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
	public int compareTo(DatasetSeasonsColumn o) {
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

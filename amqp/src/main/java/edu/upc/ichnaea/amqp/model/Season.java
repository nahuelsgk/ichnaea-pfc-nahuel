package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;
import java.util.List;

public class Season implements Iterable<SeasonTrial>, Comparable<Season> {

	protected List<SeasonTrial> mTrials;
	
	public Season() {
		this(new ArrayList<SeasonTrial>());
	}
	
	public Season(List<SeasonTrial> trials) {
		mTrials = trials;
	}
	
	@Override
	public int compareTo(Season o) {
		int c = mTrials.size()-o.mTrials.size();
		if(c == 0) {
			for(int i=0; i<mTrials.size(); i++) {
				c = mTrials.get(i).compareTo(o.mTrials.get(i));
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
		return mTrials.size();
	}
	
	public Collection<SeasonTrial> getTrials() {
		return mTrials;
	}
	
	public SeasonTrial getTrial(int index) {
		return mTrials.get(index);
	}
	
	public void addTrial(SeasonTrial trial) {
		mTrials.add(trial);
	}

	@Override
	public Iterator<SeasonTrial> iterator() {
		return mTrials.iterator();
	}
	
	public boolean isEmpty() {
		for(SeasonTrial trial : mTrials) {
			if(!trial.isEmpty()) {
				return false;
			}
		}
		return true;
	}
}
package edu.upc.ichnaea.amqp.model;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;
import java.util.List;

public class Aging implements Iterable<AgingTrial>, Comparable<Aging> {

	protected List<AgingTrial> mTrials;
	
	public Aging() {
		this(new ArrayList<AgingTrial>());
	}
	
	public Aging(List<AgingTrial> trials) {
		mTrials = trials;
	}
	
	@Override
	public int compareTo(Aging o) {
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
	
	public Collection<AgingTrial> getTrials() {
		return mTrials;
	}
	
	public AgingTrial getTrial(int index) {
		return mTrials.get(index);
	}
	
	public void addTrial(AgingTrial trial) {
		mTrials.add(trial);
	}

	@Override
	public Iterator<AgingTrial> iterator() {
		return mTrials.iterator();
	}
	
	public boolean isEmpty() {
		for(AgingTrial trial : mTrials) {
			if(!trial.isEmpty()) {
				return false;
			}
		}
		return true;
	}
}
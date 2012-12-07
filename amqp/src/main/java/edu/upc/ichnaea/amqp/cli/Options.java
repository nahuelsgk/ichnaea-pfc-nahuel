package edu.upc.ichnaea.amqp.cli;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

public class Options implements Iterable<Option>{
	
	private org.apache.commons.cli.Options mInternalOptions;
	private List<Option> mOptions = new ArrayList<Option>();
	
	public void add(Option opt) {
		mInternalOptions.addOption(opt.getInternalOption());
		mOptions.add(opt);
	}
	
	org.apache.commons.cli.Options getInternalOptions() {
		return mInternalOptions;
	}

	@Override
	public Iterator<Option> iterator() {
		return mOptions.iterator();
	}

}

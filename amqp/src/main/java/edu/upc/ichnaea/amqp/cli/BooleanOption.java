package edu.upc.ichnaea.amqp.cli;

import org.apache.commons.cli.CommandLine;

public abstract class BooleanOption extends Option {

	private boolean mDefault = false;

	public BooleanOption(String arg)
	{
		super(arg, false);
	}
	
	public BooleanOption setDefaultValue(boolean value) {
		mDefault = value;
		return this;
	}
	
	@Override
	void load(CommandLine line)  throws InvalidOptionException {
		boolean value = mDefault;
		if(inCommandLine(line)) {
			value = true;
		}
		setValue(value);
	}
	
	abstract public void setValue(boolean value) throws InvalidOptionException;
	
}

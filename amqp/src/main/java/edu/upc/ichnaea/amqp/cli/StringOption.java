package edu.upc.ichnaea.amqp.cli;

import org.apache.commons.cli.CommandLine;

public abstract class StringOption extends Option {
	
	private String mDefault;

	public StringOption(String arg) {
		super(arg, true);
	}
	
	public StringOption setDefaultValue(String value) {
		mDefault = value;
		return this;
	}
	
	@Override
	void load(CommandLine line) throws InvalidOptionException {
		String value = mDefault;
		if(inCommandLine(line)) {
			value = getCommandLineValue(line);
		}
		setValue(value);
	}
	
	abstract public void setValue(String value) throws InvalidOptionException;

}

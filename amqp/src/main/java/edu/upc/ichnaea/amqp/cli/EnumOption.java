package edu.upc.ichnaea.amqp.cli;

import org.apache.commons.cli.CommandLine;

abstract public class EnumOption<T extends Enum<T>> extends Option {
	
	private T mDefault;

	public EnumOption(String arg) {
		super(arg, true);
	}
	
	public EnumOption<T> setDefaultValue(T def) {
		mDefault = def;
		return this;
	}

	@Override
	void load(CommandLine line) throws InvalidOptionException {
		T value = mDefault;
		if(inCommandLine(line)) {
			String strValue = getCommandLineValue(line).trim();
			try {
				value = Enum.valueOf(value.getDeclaringClass(), strValue);
			}catch (IllegalArgumentException e) {
				throw new InvalidOptionException("Invalid value \""+value+"\" for option \""+getName()+"\".");
			}
		}
		setValue(value);
	}
	
	abstract public void setValue(T value) throws InvalidOptionException;

}

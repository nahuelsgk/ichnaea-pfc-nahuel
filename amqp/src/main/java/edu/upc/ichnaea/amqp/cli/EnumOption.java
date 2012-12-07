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
	void load(CommandLine line) throws OptionException {
		T value = mDefault;
		if(inCommandLine(line)) {
			value = Enum.valueOf(value.getDeclaringClass(), getCommandLineValue(line));
		}
		setValue(value);
	}
	
	abstract public void setValue(T value) throws OptionException;

}

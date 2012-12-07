package edu.upc.ichnaea.amqp.cli;

import org.apache.commons.cli.CommandLine;

public abstract class Option {
	
	private org.apache.commons.cli.Option mOption;
	private boolean mRequired = false;

	public Option(String arg, boolean hasArg) {
		mOption = new org.apache.commons.cli.Option("", arg, hasArg, "");
	}
	
	public Option setDescription(String desc) {
		mOption.setDescription(desc);
		return this;
	}
	
	public Option setRequired(boolean req) {
		mRequired = req;
		return this;
	}
	
	public boolean isRequired() {
		return mRequired;
	}
	
	public String getName()
	{
		return mOption.getLongOpt();
	}
	
	org.apache.commons.cli.Option getInternalOption() {
		return mOption;
	}
	
	protected boolean inCommandLine(CommandLine line) {
		return line.hasOption(mOption.getLongOpt());
	}

	
	protected String getCommandLineValue(CommandLine line) {
		return line.getOptionValue(mOption.getLongOpt());
	}
	
	abstract void load(CommandLine line) throws OptionException;

}

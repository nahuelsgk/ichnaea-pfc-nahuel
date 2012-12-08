package edu.upc.ichnaea.amqp.cli;

import org.apache.commons.cli.CommandLine;

public abstract class Option {
	
	private char mChar = ' ';
	private String mName;
	private String mDescription;
	private boolean mHasArgument = true;
	private boolean mRequired = false;
	private org.apache.commons.cli.Option mOption;

	public Option(String name, boolean hasArg) {
		mName = name;
		mHasArgument = hasArg;
	}
	
	public Option setDescription(String desc) {
		mDescription = desc;
		return this;
	}
	
	public Option setRequired(boolean req) {
		mRequired = req;
		return this;
	}
	
	public boolean isRequired() {
		return mRequired;
	}
	
	public String getName() {
		return mName;
	}
	
	public boolean hasChar() {
		return mChar != ' ';
	}
	
	public char getChar() {
		return mChar;
	}
	
	public void setChar(char ch) {
		mChar = ch;
	}
	
	public char[] getSuggestedChars() {
		 return new StringBuffer(getName()).reverse().toString().toCharArray();
	}
	
	org.apache.commons.cli.Option getInternalOption() {
		if(mOption == null) {
			mOption = new org.apache.commons.cli.Option(""+mChar, mName, mHasArgument, mDescription);
		}
		return mOption;
	}
	
	protected boolean inCommandLine(CommandLine line) {
		return line.hasOption(mOption.getLongOpt());
	}
	
	protected String getCommandLineValue(CommandLine line) {
		return line.getOptionValue(mOption.getLongOpt());
	}
	
	abstract void load(CommandLine line) throws InvalidOptionException;

}

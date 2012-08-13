package edu.upc.ichnaea.shell;

public class Command {

	protected String mCommand;
	
	public Command(String command)
	{
		mCommand = command;
	}
	
	public String toString()
	{
		return mCommand;
	}
}

package edu.upc.ichnaea.shell;

public class CommandResult {

	protected String mOutput;
	protected String mError;
	protected int mExitStatus;
	
	public CommandResult(String output, String error, int status)
	{
		mOutput = output;
		mError = error;
		mExitStatus = status;
	}
	
	public String getOutput()
	{
		return mOutput;
	}
	
	public String getError()
	{
		return mError;
	}
	
	public int getExitStatus()
	{
		return mExitStatus;
	}
}

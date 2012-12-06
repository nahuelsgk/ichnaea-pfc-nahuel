package edu.upc.ichnaea.shell;

abstract public class IchnaeaCommand implements CommandInterface {
	
	private String mScriptPath;
	
	abstract protected String getParameters();
	
	public void setScriptPath(String path)
	{
		mScriptPath = path;
	}
	
	public String toString()
	{
		return mScriptPath+" "+getParameters();
	}
	
	@Override
	public void beforeRun(ShellInterface shell)
	{
	}
	
}

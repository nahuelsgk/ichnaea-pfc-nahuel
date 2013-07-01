package edu.upc.ichnaea.shell;

abstract public class IchnaeaCommand implements CommandInterface {
	
	private String mScriptPath;
	
	abstract protected String getParameters();
	
	public IchnaeaCommand setScriptPath(String path) {
		mScriptPath = path;
		return this;
	}
	
	public String toString() {
		return mScriptPath+" "+getParameters();
	}
	
	@Override
	public void beforeRun(ShellInterface shell) {
	}
	
}

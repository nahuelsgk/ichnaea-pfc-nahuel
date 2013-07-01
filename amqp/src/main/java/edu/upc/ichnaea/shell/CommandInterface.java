package edu.upc.ichnaea.shell;

public interface CommandInterface {
	
	public void beforeRun(ShellInterface shell);
	public String toString();
}

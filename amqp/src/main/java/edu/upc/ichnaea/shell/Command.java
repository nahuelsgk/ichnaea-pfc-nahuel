package edu.upc.ichnaea.shell;

public class Command implements CommandInterface {

	protected String mCommand;
	
	public Command(String command) {
		mCommand = command;
	}
	
	public String toString() {
		return mCommand;
	}

	@Override
	public void beforeRun(ShellInterface shell) {
	}
}
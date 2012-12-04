package edu.upc.ichnaea.amqp.worker;

import java.io.IOException;

import edu.upc.ichnaea.shell.CommandInterface;
import edu.upc.ichnaea.shell.ShellInterface;

abstract public class ShellCommandWorker extends Worker {
	
	private ShellInterface mShell; 

	protected ShellInterface getShell()
	{
		return mShell;
	}
	
	protected void setShell(ShellInterface shell)
	{
		mShell = shell;
	}
	
	protected boolean runCommand(CommandInterface cmd) throws IOException {
		try {
			getShell().run(cmd);
			return true;
		} catch (InterruptedException e) {
			return false;
		}
	}

}

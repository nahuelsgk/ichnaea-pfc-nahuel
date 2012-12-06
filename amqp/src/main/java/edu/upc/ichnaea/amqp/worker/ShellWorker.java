package edu.upc.ichnaea.amqp.worker;

import java.io.IOException;
import java.net.MalformedURLException;
import java.util.Map;

import edu.upc.ichnaea.shell.CommandInterface;
import edu.upc.ichnaea.shell.CommandResult;
import edu.upc.ichnaea.shell.ShellFactory;
import edu.upc.ichnaea.shell.ShellInterface;

abstract public class ShellWorker extends Worker {
	
	private ShellInterface mShell; 

	protected ShellInterface getShell() throws IOException
	{
		if(mShell == null)
		{
			throw new IOException("No shell found");
		}		
		return mShell;
	}
	
	public void setShell(ShellInterface shell)
	{
		mShell = shell;
	}
	
	public void setShell(ShellFactory.Type type, Map<String,String> options) throws MalformedURLException
	{
		setShell(new ShellFactory().create(type, options));
	}
	
	protected CommandResult runCommand(CommandInterface cmd) throws IOException, InterruptedException {
		return getShell().run(cmd);
	}

}

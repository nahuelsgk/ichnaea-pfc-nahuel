package edu.upc.ichnaea.amqp.worker;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.net.MalformedURLException;
import java.util.Map;

import edu.upc.ichnaea.shell.CommandInterface;
import edu.upc.ichnaea.shell.CommandResult;
import edu.upc.ichnaea.shell.ShellFactory;
import edu.upc.ichnaea.shell.ShellInterface;

abstract public class ShellWorker extends Worker {
	
	private ShellInterface mShell; 

	public ShellWorker(ShellInterface shell) {
		mShell = shell;
	}
	
	public ShellWorker(Map<String,String> options) throws MalformedURLException {
		this(new ShellFactory().create(options));
	}
	
	
	public ShellWorker(String url) throws MalformedURLException {
		this(new ShellFactory().create(url));
	}	
	
	protected ShellInterface getShell() {	
		return mShell;
	}
	
	protected FileOutputStream writeFile(String path) {
		return getShell().writeFile(path);
	}
	
	protected FileInputStream readFile(String path) throws FileNotFoundException {
		return getShell().readFile(path);
	}
	
	protected CommandResult runCommand(CommandInterface cmd) throws InterruptedException, IOException {
		return getShell().run(cmd);
	}

}
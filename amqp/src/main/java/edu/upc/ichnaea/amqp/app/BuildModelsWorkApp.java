package edu.upc.ichnaea.amqp.app;

import java.net.MalformedURLException;

import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.Option;
import org.apache.commons.cli.Options;
import org.apache.commons.cli.ParseException;

import edu.upc.ichnaea.amqp.worker.BuildModelsWorker;
import edu.upc.ichnaea.amqp.worker.WorkerInterface;
import edu.upc.ichnaea.shell.SecureShell;
import edu.upc.ichnaea.shell.Shell;

public class BuildModelsWorkApp extends WorkApp {

	Option mOptionShell = new Option("s", "shell", true, "The url to the remote shell.");
	String mShell;
	
	
    protected Options getOptions()
    {
    	Options options = super.getOptions();
    	options.addOption(mOptionShell);
    	return options;
    }
    
    public void setShell(String shell)
    {
    	mShell = shell;
    }
    
    @Override
    protected CommandLine parseArguments(String[] args) throws ParseException
    {
    	CommandLine line = super.parseArguments(args);
    	setShell(mOptionShell.getValue(mShell));
    	return line;
    }     
	
	@Override
	protected WorkerInterface createWorker() {
		BuildModelsWorker worker = new BuildModelsWorker();
		if(mShell == null) {
			worker.setShell(new Shell());	
		} else {
			try {
				worker.setShell(new SecureShell(mShell));
			} catch (MalformedURLException e) {
			}
		}
		return worker;
	}
	
}

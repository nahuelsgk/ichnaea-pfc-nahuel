package edu.upc.ichnaea.amqp.app;

import java.io.IOException;
import java.net.MalformedURLException;

import edu.upc.ichnaea.amqp.cli.InvalidOptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;
import edu.upc.ichnaea.amqp.client.BuildModelsProcessClient;
import edu.upc.ichnaea.shell.ShellFactory;
import edu.upc.ichnaea.shell.ShellInterface;

public class BuildModelsProcessApp extends QueueApp {

	BuildModelsProcessClient mClient;
	String mShell;
	String mScriptPath;
	
    public static void main(String[] args) {   	
    	main(args, new BuildModelsProcessApp());
    }	
	
    protected Options getOptions() {
    	Options options = super.getOptions();
    	options.add(new StringOption("shell") {
			@Override
			public void setValue(String value) throws InvalidOptionException {
				mShell = value;
			}
		}.setDescription("The url to the remote shell."));
    	
    	options.add(new StringOption("ichnaea-script") {
			@Override
			public void setValue(String value) throws InvalidOptionException {
				mScriptPath = value;
			}
		}.setDefaultValue("/usr/local/ichnaea.sh").setDescription("The path to the ichnaea script."));    	
    	return options;
    }
    
    

	@Override
	protected void setup() throws IOException {
		super.setup();
		ShellInterface shell = null;
		try {
			shell = new ShellFactory().create(mShell);
		} catch (MalformedURLException e) {
			throw new InvalidOptionException(e.getMessage());
		}
		mClient = new BuildModelsProcessClient(shell, mScriptPath, getQueueName());
		mClient.setup(getChannel());
	}
	
	@Override
	protected void start() throws IOException
	{
		super.start();
		runClient(mClient);
	}	
	
}

package edu.upc.ichnaea.amqp.app;

import java.net.MalformedURLException;

import edu.upc.ichnaea.amqp.cli.InvalidOptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;
import edu.upc.ichnaea.amqp.worker.BuildModelsWorker;
import edu.upc.ichnaea.amqp.worker.WorkerInterface;
import edu.upc.ichnaea.shell.ShellFactory;
import edu.upc.ichnaea.shell.ShellInterface;

public class BuildModelsWorkApp extends WorkApp {

	ShellInterface mShell;
	
    protected Options getOptions()
    {
    	Options options = super.getOptions();
    	options.add(new StringOption("shell") {
			@Override
			public void setValue(String value) throws InvalidOptionException {
				try {
					mShell = new ShellFactory().create(value);
				} catch (MalformedURLException e) {
					throw new InvalidOptionException(e.getMessage());
				}
			}
		}.setRequired(true).setDescription("The url to the remote shell."));
    	return options;
    }
        
	@Override
	protected WorkerInterface createWorker() {
		return new BuildModelsWorker(mShell);	
	}
	
}

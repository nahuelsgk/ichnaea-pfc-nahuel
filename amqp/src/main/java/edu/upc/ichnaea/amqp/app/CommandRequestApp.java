package edu.upc.ichnaea.amqp.app;

import java.io.IOException;

import com.rabbitmq.client.MessageProperties;

import edu.upc.ichnaea.amqp.cli.OptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;

public class CommandRequestApp extends QueueApp {

	protected String mCommand;
	
    public static void main(String[] args)
    {
    	main(args, new CommandRequestApp());
    }
    
	
    protected Options getOptions()
    {
    	Options options = super.getOptions();
    	options.add(new StringOption("command"){
			@Override
			public void setValue(String value) throws OptionException {
				mCommand = value;
			}
    	}.setRequired(true).setDescription("The command to run on the amqp server"));
    	return options;
    }

	@Override
	protected void start() throws IOException
	{
		getChannel().basicPublish("", getQueueName(),
				MessageProperties.PERSISTENT_TEXT_PLAIN , mCommand.getBytes());  
	}

}

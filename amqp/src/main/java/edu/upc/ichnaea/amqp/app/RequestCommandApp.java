package edu.upc.ichnaea.amqp.app;

import java.io.IOException;

import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.Option;
import org.apache.commons.cli.ParseException;

import com.rabbitmq.client.MessageProperties;

public class RequestCommandApp extends QueueApp {

	Option mOptionCommand = new Option("c", "command", true, "The command to run on the amqp server");
	protected String mCommand;
	
    public static void main(String[] args)
    {
    	main(args, new RequestCommandApp());
    }	
	
	@Override
    protected CommandLine parseArguments(String[] args) throws ParseException
    {
		CommandLine line = super.parseArguments(args);
		setCommand(mOptionCommand.getValue(mCommand));
		return line;
	}
	
	public void setCommand(String command)
	{
		mCommand = command;
	}

	@Override
	protected void start() throws IOException
	{
		getChannel().basicPublish("", getQueueName(),
				MessageProperties.PERSISTENT_TEXT_PLAIN , mCommand.getBytes());  
	}

}

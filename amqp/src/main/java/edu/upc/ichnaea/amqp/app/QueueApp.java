package edu.upc.ichnaea.amqp.app;

import java.io.IOException;

import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.Option;
import org.apache.commons.cli.Options;
import org.apache.commons.cli.ParseException;

import com.rabbitmq.client.Channel;

public abstract class QueueApp extends App
{
	Option mOptionQueue = new Option("q", "queue", true, "The queue of the amqp server");
	
	private Channel mChannel;
	private String mQueueName = "default";

    protected Options getOptions()
    {
    	Options options = super.getOptions();
    	options.addOption(mOptionQueue);
    	return options;
    }	
    
    protected CommandLine parseArguments(String[] args) throws ParseException
    {
    	CommandLine line = super.parseArguments(args);
    	setQueueName(mOptionQueue.getValue(mQueueName));
    	return line;
    }    
    
    protected void setup() throws IOException
    {
        mChannel = mConnection.createChannel();
        mChannel.queueDeclare(mQueueName, false, false, false, null);
    }
    
    protected void end() throws IOException
    {
	    mConnection.close();    	
	    super.end();
    }
    
    public void setQueueName(String queueName)
    {
    	mQueueName = queueName;
    }
    
    protected Channel getChannel()
    {
    	return mChannel;
    }
    
    protected String getQueueName()
    {
    	return mQueueName;
    }
}

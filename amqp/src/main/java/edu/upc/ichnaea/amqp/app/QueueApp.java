package edu.upc.ichnaea.amqp.app;

import java.io.IOException;

import com.rabbitmq.client.Channel;

import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;

public abstract class QueueApp extends App
{
	private Channel mChannel;
	private String mQueueName;

    protected Options getOptions()
    {
    	Options options = super.getOptions();
    	options.add(new StringOption("queue"){
			@Override
			public void setValue(String value) {
				mQueueName = value;
			}
    	}.setDefaultValue("default").setDescription("The name of the amqp server queue to send messages to."));
    	return options;
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
    
    protected Channel getChannel()
    {
    	return mChannel;
    }
    
    protected String getQueueName()
    {
    	return mQueueName;
    }
}

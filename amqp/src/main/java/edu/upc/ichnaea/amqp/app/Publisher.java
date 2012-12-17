package edu.upc.ichnaea.amqp.app;

import java.io.IOException;

import com.rabbitmq.client.Channel;

import edu.upc.ichnaea.amqp.requester.MessageInterface;
import edu.upc.ichnaea.amqp.requester.RequesterInterface;

public abstract class Publisher extends Queue
{
	RequesterInterface mRequester;
	
    abstract protected RequesterInterface createRequester();
	
	@Override
    protected void setup() throws IOException
    {
		super.setup();
    	System.out.println("Setting up queue publisher app...");
    	mRequester = createRequester();
    }

	@Override
	protected void start() throws IOException
	{
		super.start();
    	System.out.println("Starting queue publisher app...");
    	Channel ch = getChannel();
    	String queueName = getQueueName();
    	while(true){
    		MessageInterface msg = mRequester.request();
    		ch.basicPublish("", queueName, msg.getProperties(), msg.getBody());    		
    	}
	}   
}
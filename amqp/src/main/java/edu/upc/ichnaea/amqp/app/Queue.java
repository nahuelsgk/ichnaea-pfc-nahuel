package edu.upc.ichnaea.amqp.app;

import java.io.IOException;
import java.util.List;

import com.rabbitmq.client.Channel;

public abstract class Queue extends App
{
	protected Channel mChannel;
	protected String mQueueName;

    protected String getDefaultQueueName()
    {
    	return "default";
    }
    
    public void setArguments(List<String> args)
    {
    	super.setArguments(args);
    	String queueName = getDefaultQueueName();
    	if(args.size() > 0){
    		queueName = args.get(0);
    		args.remove(0);
    	}
        setQueueName(queueName);        
    }    
    
    protected void setup() throws IOException
    {
        mChannel = mConnection.createChannel();
        mChannel.queueDeclare(mQueueName, false, false, false, null);
    }
    
    protected void end() throws IOException
    {
	    super.end();
	    mConnection.close();
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

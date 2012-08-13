package edu.upc.ichnaea.amqp.worker;

import com.rabbitmq.client.Channel;

public abstract class Worker implements WorkerInterface
{
	protected Channel mChannel = null;
	
	public void setChannel(Channel channel)
	{
		mChannel = channel;
	}
	
	protected boolean hasChannel()
	{
		return mChannel != null;
	}
	
	protected Channel getChannel()
	{
		if(!hasChannel()){
			throw new RuntimeException("No channel defined.");
		}
		return mChannel;
	}
	
}

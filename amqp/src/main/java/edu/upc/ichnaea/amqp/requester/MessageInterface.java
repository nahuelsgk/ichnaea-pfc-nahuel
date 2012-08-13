package edu.upc.ichnaea.amqp.requester;

import com.rabbitmq.client.AMQP.BasicProperties;


public interface MessageInterface
{
	public BasicProperties getProperties();
	
	public byte[] getBody();
}

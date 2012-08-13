package edu.upc.ichnaea.amqp.requester;

import com.rabbitmq.client.AMQP.BasicProperties;
import com.rabbitmq.client.MessageProperties;

public class StringMessage implements MessageInterface
{
	protected String mBody;
	
	public StringMessage(String body)
	{
		mBody = body;
	}
	
	public BasicProperties getProperties()
	{
		return MessageProperties.PERSISTENT_TEXT_PLAIN;
	}
	
	public byte[] getBody()
	{
		return mBody.getBytes();
	}
}

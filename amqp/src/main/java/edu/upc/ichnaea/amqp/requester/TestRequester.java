package edu.upc.ichnaea.amqp.requester;

import java.io.IOException;

public class TestRequester extends Requester
{
	int mCounter = 0;
	
	@Override
	public MessageInterface request() throws IOException
	{
		String msg = "request "+mCounter;
		System.out.println("Message: " + msg);
		mCounter++;
		return new StringMessage(msg);
	}
}
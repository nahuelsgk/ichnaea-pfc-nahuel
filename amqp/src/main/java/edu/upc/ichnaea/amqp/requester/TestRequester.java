package edu.upc.ichnaea.amqp.requester;

import java.io.IOException;

public class TestRequester extends Requester
{
	int mCounter = 0;
	int mCounterMax = 0;
	MessageInterface mMessage;
	
	public TestRequester(MessageInterface msg, int max)
	{
		mMessage = msg;
		mCounterMax = max;
	}
	
	@Override
	public MessageInterface request() throws IOException
	{
		mCounter++;
		return mMessage;
	}
	
	public boolean finished()
	{
		return mCounter >= mCounterMax;
	}
}
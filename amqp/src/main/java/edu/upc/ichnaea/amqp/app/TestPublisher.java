package edu.upc.ichnaea.amqp.app;

import edu.upc.ichnaea.amqp.requester.RequesterInterface;
import edu.upc.ichnaea.amqp.requester.TestRequester;

public class TestPublisher extends Publisher
{
    public static void main(String[] args)
    {
    	main(args, new TestPublisher());
    }
	
	@Override
	protected RequesterInterface createRequester()
	{
		return new TestRequester();
	}
}
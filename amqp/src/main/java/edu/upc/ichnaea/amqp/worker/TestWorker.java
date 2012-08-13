package edu.upc.ichnaea.amqp.worker;

import java.io.IOException;

import com.rabbitmq.client.QueueingConsumer.Delivery;

public class TestWorker extends Worker
{
	@Override
	public void process(Delivery delivery) throws IOException
	{
		String msg = new String(delivery.getBody());
    	System.out.println("Message: " + msg);
	}
}

package edu.upc.ichnaea.amqp.worker;

import java.io.IOException;

import com.rabbitmq.client.Channel;
import com.rabbitmq.client.QueueingConsumer.Delivery;

public interface WorkerInterface
{	
	public void setChannel(Channel channel);
	
	public void process(Delivery delivery) throws IOException;
}
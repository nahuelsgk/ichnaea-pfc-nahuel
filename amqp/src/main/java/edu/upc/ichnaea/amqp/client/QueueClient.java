package edu.upc.ichnaea.amqp.client;

import java.io.IOException;

import com.rabbitmq.client.Channel;

abstract public class QueueClient extends Client {

	private String mQueue;
	private boolean mWithExchange = false;
	
	public QueueClient(String queue, boolean withExchange) {
		mQueue = queue;
		mWithExchange = withExchange;
	}
	
	protected String getQueue() {
		return mQueue;
	}
	
	protected String getExchange() {
		if(mWithExchange) {
			return mQueue;
		} else {
			return "";
		}
	}
	
	@Override
	public void setup(Channel channel) throws IOException {
		super.setup(channel);		
		channel.queueDeclare(mQueue, false, false, false, null);
		if(mWithExchange) {
			channel.exchangeDeclare(mQueue, "direct", true);	
		}
	}
	
	protected String routingQueueDeclare(String routingKey) throws IOException {
		Channel ch = getChannel();
		String queue = ch.queueDeclare().getQueue();
		ch.queueBind(queue, getExchange(), routingKey);
		return queue;
	}

}

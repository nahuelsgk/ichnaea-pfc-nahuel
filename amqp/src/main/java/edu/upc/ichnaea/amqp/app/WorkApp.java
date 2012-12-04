package edu.upc.ichnaea.amqp.app;

import java.io.IOException;

import com.rabbitmq.client.Channel;
import com.rabbitmq.client.ConsumerCancelledException;
import com.rabbitmq.client.QueueingConsumer;
import com.rabbitmq.client.QueueingConsumer.Delivery;
import com.rabbitmq.client.ShutdownSignalException;

import edu.upc.ichnaea.amqp.worker.WorkerInterface;

public abstract class WorkApp extends QueueApp
{
	WorkerInterface mWorker;
	
    abstract protected WorkerInterface createWorker();
    
    @Override
	protected void setup() throws IOException
	{
		super.setup();    	
		System.out.println("Setting up queue work app...");		
        mWorker = createWorker(); 
        mWorker.setChannel(getChannel());
	}
	
	@Override
	protected void start() throws IOException
	{
		super.start();
		System.out.println("Starting queue work app...");		
        final Channel ch = getChannel();

        QueueingConsumer consumer = new QueueingConsumer(ch);
        ch.basicConsume(getQueueName(), consumer);
        while (true) {
            try {
            	Delivery delivery = consumer.nextDelivery();
				mWorker.process(delivery);
		    	ch.basicAck(delivery.getEnvelope().getDeliveryTag(), false);
			} catch (ShutdownSignalException e) {
				break;
			} catch (ConsumerCancelledException e) {
				continue;
			} catch (InterruptedException e) {
				break;
			}
        }
    }
    
}

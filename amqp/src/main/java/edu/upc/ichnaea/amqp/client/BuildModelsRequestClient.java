package edu.upc.ichnaea.amqp.client;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;

import com.rabbitmq.client.AMQP;
import com.rabbitmq.client.AMQP.BasicProperties;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsRequestWriter;


public class BuildModelsRequestClient extends QueueClient {
	
	BuildModelsRequest mRequest;
	
	public BuildModelsRequestClient(BuildModelsRequest request, String queue) {
		super(queue, true);
		mRequest = request;		
	}

	@Override
	public void run() throws IOException {
		Channel ch = getChannel();
		String routingKey = getUniqueId();
		String routingQueue = routingQueueDeclare(routingKey);	
		
		ch.basicConsume(routingQueue, new DefaultConsumer(ch){
			@Override
			public void handleDelivery(String consumerTag,
				Envelope envelope,
                AMQP.BasicProperties properties,
                byte[] body)
                throws IOException
                {
					getLogger().info("got build models response...");
                }
		});
		
		BasicProperties props = new AMQP.BasicProperties.Builder()
        	.contentType("text/xml").replyTo(routingKey).build();
		
		getLogger().info("sending build models request...");
		try {
			String xml = new XmlBuildModelsRequestWriter().build(mRequest).toString();
			ch.basicPublish("", getQueue(), props, xml.getBytes());
		} catch (ParserConfigurationException e) {
			throw new IOException(e);
		}
		
		getLogger().info("waiting for build models response...");
	}

}

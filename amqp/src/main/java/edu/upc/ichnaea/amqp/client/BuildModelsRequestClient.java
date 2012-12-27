package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import java.io.OutputStream;
import java.text.SimpleDateFormat;

import javax.mail.MessagingException;
import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;
import com.rabbitmq.client.AMQP.BasicProperties;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsRequestWriter;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsResponseReader;


public class BuildModelsRequestClient extends QueueClient {
	
	BuildModelsRequest mRequest;
	OutputStream mResponseOutput;
	
	public BuildModelsRequestClient(BuildModelsRequest request, String queue) {
		super(queue, true);
		mRequest = request;
	}
	
	protected void request(String routingKey) throws ParserConfigurationException, IOException {
		getLogger().info("sending build models request...");
		
		Channel ch = getChannel();
		BasicProperties props = new AMQP.BasicProperties.Builder()
    	.contentType("text/xml").replyTo(routingKey).build();

		String xml = new XmlBuildModelsRequestWriter().build(mRequest).toString();
		ch.basicPublish("", getQueue(), props, xml.getBytes());
	}
	
	protected void response(byte[] body) throws IOException, SAXException, MessagingException {
		BuildModelsResponse resp = new XmlBuildModelsResponseReader().read(new String(body));
		float progress = resp.getProgress();
		
		if(progress < 1) {
			int percent = Math.round(progress*100);
			SimpleDateFormat f = new SimpleDateFormat("dd-MM-yyyy hh:mm");
			getLogger().info(percent+"% "+f.format(resp.getEnd().getTime()));
		} else {
			getLogger().info("request finished.");
			if(mResponseOutput != null) {
				getLogger().info("writing build models response to a file ...");
				mResponseOutput.write(body);
				mResponseOutput.close();
			}
			setFinished(true);
		}
	}

	@Override
	public void run() throws IOException {
		Channel ch = getChannel();
		String routingKey = String.valueOf(mRequest.getId());
		String routingQueue = routingQueueDeclare(routingKey);	
		
		ch.basicConsume(routingQueue, new DefaultConsumer(ch){
			@Override
			public void handleDelivery(String consumerTag,
				Envelope envelope,
                AMQP.BasicProperties properties,
                byte[] body)
                throws IOException
                {
					try {
						response(body);
					} catch (Exception e) {
						throw new IOException(e);
					}
                }
		});
		
		try {
			request(routingKey);
		}catch(Exception e) {
			throw new IOException(e);
		}
		
		getLogger().info("waiting for build models response...");
	}

}

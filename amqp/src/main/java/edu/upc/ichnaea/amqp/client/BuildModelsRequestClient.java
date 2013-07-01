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


public class BuildModelsRequestClient extends Client {
	
	BuildModelsRequest mRequest;
	OutputStream mResponseOutput;
	String mRequestExchange;
	String mRequestQueue;
	String mResponseQueue;
	
	public BuildModelsRequestClient(BuildModelsRequest request, String requestQueue, String requestExchange, String responseQueue, OutputStream output) {
		mRequest = request;
		mResponseOutput = output;
		mRequestQueue = requestQueue;		
		mRequestExchange = requestExchange;
		mResponseQueue = responseQueue;
	}
	
	protected void sendRequest(String routingKey) throws ParserConfigurationException, IOException {
		getLogger().info("sending build models request to exchange \""+mRequestExchange+"\"...");
		
		Channel ch = getChannel();
		BasicProperties props = new AMQP.BasicProperties.Builder()
    	.contentType("text/xml").replyTo(routingKey).build();

		String xml = new XmlBuildModelsRequestWriter().build(mRequest).toString();
		ch.basicPublish(mRequestExchange, "", props, xml.getBytes());
	}
	
	protected void processResponse(byte[] body) throws IOException, SAXException, MessagingException {
		BuildModelsResponse resp = new XmlBuildModelsResponseReader().read(new String(body));
		
		if(resp.hasError()) {
			getLogger().warning("got error: "+resp.getError());
			setFinished(true);
			return;
		}
		
		float progress = resp.getProgress();
		SimpleDateFormat f = new SimpleDateFormat("EEE MMM dd HH:mm:ss z yyyy");
		
		if(progress < 1) {
			getLogger().info("request update");
			int percent = Math.round(progress*100);
			getLogger().info("progress: "+percent+"%");
			if(resp.hasEnd()) {
				getLogger().info("estimated end time: "+f.format(resp.getEnd().getTime()));
			}
		} else {
			getLogger().info("request finished");
			if(resp.hasStart()) {
				getLogger().info("start time: "+f.format(resp.getStart().getTime()));
			}
			if(resp.hasEnd()) {
				getLogger().info("end time: "+f.format(resp.getEnd().getTime()));
			}
			if(mResponseOutput != null && resp.hasData()) {
				getLogger().info("writing build models response to a file ...");
				mResponseOutput.write(resp.getData());
				mResponseOutput.close();
			}
			setFinished(true);
		}
	}
	
	@Override
	public void setup(Channel channel) throws IOException {
		super.setup(channel);
		channel.queueDeclare(mRequestQueue, false, false, false, null);
		channel.exchangeDeclare(mRequestExchange, "direct", true);
		channel.queueBind(mRequestQueue, mRequestExchange, "");
		channel.queueDeclare(mResponseQueue, false, false, false, null);
	}

	@Override
	public void run() throws IOException {
		Channel ch = getChannel();
		String routingKey = mRequest.getId();	
		boolean autoAck = true;

		ch.basicConsume(mResponseQueue, autoAck, new DefaultConsumer(ch){
			@Override
			public void handleDelivery(String consumerTag,
				Envelope envelope,
                AMQP.BasicProperties properties,
                byte[] body)
                throws IOException
                {
					try {
						processResponse(body);
					} catch (Exception e) {
						throw new IOException(e);
					}
                }
		});
		
		try {
			sendRequest(routingKey);
		}catch(Exception e) {
			throw new IOException(e);
		}
		
		getLogger().info("waiting for build models response on queue \""+mResponseQueue+"\"...");
	}

}

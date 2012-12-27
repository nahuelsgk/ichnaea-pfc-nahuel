package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import java.util.UUID;
import java.util.logging.Logger;

import com.rabbitmq.client.Channel;

import edu.upc.ichnaea.amqp.app.App;

public abstract class Client implements ClientInterface {

	private Channel mChannel;
	private boolean mFinished = false;
	private static Logger LOGGER = Logger.getLogger(App.class.getName());
	
    public static Logger getLogger() {
    	return LOGGER;
    }
	
	protected Channel getChannel() {
		return mChannel;
	}
	
	protected String getUniqueId() {
		return UUID.randomUUID().toString();
	}

	@Override
	public void setup(Channel channel) throws IOException {
		mChannel = channel;
	}
	
	protected void setFinished(boolean finished) {
		mFinished = finished;
	}
	
	public boolean hasFinished() {
		return mFinished;
	}
}
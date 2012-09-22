package edu.upc.ichnaea.amqp.app;

import java.io.IOException;

import com.rabbitmq.client.Channel;

public class TestBuildModelsRequester extends App {
	
	protected Channel mChannel;
	
	protected String mInputQueue = "build_models";	
	
    public static void main(String[] args)
    {
    	main(args, new TestBuildModelsRequester());
    }		

	@Override
	protected void setup() throws IOException {
		super.setup();
		mChannel = getConnection().createChannel();
        mChannel.queueDeclare(mInputQueue, false, false, false, null);		
	}

	@Override
	protected void start() throws IOException {

	}

	@Override
	protected void end() throws IOException {
		mChannel.close();
		super.end();
	}    
}

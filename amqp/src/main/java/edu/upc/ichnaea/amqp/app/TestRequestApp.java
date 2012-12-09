package edu.upc.ichnaea.amqp.app;

import java.io.IOException;

import com.rabbitmq.client.MessageProperties;

import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;

public class TestRequestApp extends QueueApp {

	protected String mMessage;
	
    public static void main(String[] args)
    {
    	main(args, new TestRequestApp());
    }
    
    protected Options getOptions()
    {
    	Options options = super.getOptions();
    	options.add(new StringOption("message"){
			@Override
			public void setValue(String value) {
				mMessage = value;
			}
    	}.setRequired(true).setDescription("The message to send to the amqp server"));
    	return options;
    }

	@Override
	protected void start() throws IOException
	{
		getChannel().basicPublish("", getQueueName(),
				MessageProperties.PERSISTENT_TEXT_PLAIN , mMessage.getBytes());  
	}

}

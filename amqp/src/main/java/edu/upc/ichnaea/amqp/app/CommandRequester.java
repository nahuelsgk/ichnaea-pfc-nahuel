package edu.upc.ichnaea.amqp.app;

import java.io.IOException;
import java.security.InvalidParameterException;
import java.util.List;

import com.rabbitmq.client.Channel;
import com.rabbitmq.client.MessageProperties;

public class CommandRequester extends App {

	protected Channel mChannel;
	
	protected String mCommand;
	
	protected String mInputQueue = "input";
	
    public static void main(String[] args)
    {
    	main(args, new CommandRequester());
    }	
	
	@Override
	public void setArguments(List<String> args) {
    	if(args.size() == 0){
    		throw new InvalidParameterException("No command supplied.");
    	}
		setCommand(args.get(0));
		args.remove(0);
	}
	
	public void setCommand(String command) {
		mCommand = command;
	}

	@Override
	protected void setup() throws IOException {
		super.setup();
		mChannel = getConnection().createChannel();
        mChannel.queueDeclare(mInputQueue, false, false, false, null);		
	}

	@Override
	protected void start() throws IOException {
		mChannel.basicPublish("", mInputQueue,
				MessageProperties.PERSISTENT_TEXT_PLAIN , mCommand.getBytes());  
	}

	@Override
	protected void end() throws IOException {
		mChannel.close();
		super.end();
	}

}

package edu.upc.ichnaea.amqp.app;

import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.Option;
import org.apache.commons.cli.Options;
import org.apache.commons.cli.ParseException;

import edu.upc.ichnaea.amqp.requester.RequesterInterface;
import edu.upc.ichnaea.amqp.requester.StringMessage;
import edu.upc.ichnaea.amqp.requester.TestRequester;

public class TestRequestApp extends RequestApp
{
	Option mOptionMessage = new Option("m", "msg", true, "The message to send to the amqp server.");
	Option mOptionNum = new Option("n", "num", true, "The amount of messages to send to the amqp server.");
	String mMessage = "";
	int mMessageNum = 0;
	
    protected Options getOptions()
    {
    	Options options = super.getOptions();
    	options.addOption(mOptionMessage);
    	return options;
    }	
	
    public static void main(String[] args)
    {
    	main(args, new TestRequestApp());
    }
    
    @Override
    protected CommandLine parseArguments(String[] args) throws ParseException
    {
    	CommandLine line = super.parseArguments(args);
    	setMessage(mOptionMessage.getValue(mMessage));
    	setMessageNum(Integer.parseInt(mOptionMessage.getValue()));
    	return line;
    }    
    
    public void setMessage(String msg)
    {
    	mMessage = msg;
    }
    
    public void setMessageNum(int num)
    {
    	mMessageNum = num;
    }
	
	@Override
	protected RequesterInterface createRequester()
	{
		return new TestRequester(new StringMessage(mMessage), mMessageNum);
	}
}
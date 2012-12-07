package edu.upc.ichnaea.amqp.app;

import edu.upc.ichnaea.amqp.cli.IntegerOption;
import edu.upc.ichnaea.amqp.cli.OptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;
import edu.upc.ichnaea.amqp.requester.RequesterInterface;
import edu.upc.ichnaea.amqp.requester.StringMessage;
import edu.upc.ichnaea.amqp.requester.TestRequester;

public class TestRequestApp extends RequestApp
{
	String mMessage = "";
	int mMessageNum = 0;
	
    protected Options getOptions()
    {
    	Options options = super.getOptions();
    	options.add(new StringOption("msg"){
			@Override
			public void setValue(String value) throws OptionException {
				mMessage = value;
			}
    	}.setRequired(true).setDescription("The message to send to the amqp server."));
    	options.add(new IntegerOption("num"){
			@Override
			public void setValue(int value) throws OptionException {
				mMessageNum = value;
			}
    	}.setDefaultValue(mMessageNum).setDescription("The amount of messages to send to the amqp server."));
    	return options;
    }
	
    public static void main(String[] args)
    {
    	main(args, new TestRequestApp());
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
package edu.upc.ichnaea.amqp.app;

import java.io.IOException;
import java.net.URISyntaxException;
import java.security.KeyManagementException;
import java.security.NoSuchAlgorithmException;

import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.CommandLineParser;
import org.apache.commons.cli.GnuParser;
import org.apache.commons.cli.HelpFormatter;
import org.apache.commons.cli.Option;
import org.apache.commons.cli.Options;
import org.apache.commons.cli.ParseException;

import com.rabbitmq.client.Connection;
import com.rabbitmq.client.ConnectionFactory;

public abstract class App
{
	Option mOptionUri = new Option("u", "uri", true, "Uri for the amqp server");
	protected Connection mConnection;
	protected String mUri;

    public static void main(String[] args, App app)
    {
    	try {
    		app.parseArguments(args);
	        app.connect();
	        app.start();
	        app.end();
    	} catch (ParseException e) {
    		System.err.println("Could not parse arguments: " + e);
    		HelpFormatter formatter = new HelpFormatter();
    		formatter.printHelp( args[0], app.getOptions() );    		
            System.exit(1);
    	} catch (Exception e) {
            System.err.println("Main thread caught exception: " + e);
            e.printStackTrace();
            System.exit(1);
        }   	
    }
    
    protected void connect()
    	throws KeyManagementException, NoSuchAlgorithmException, URISyntaxException, IOException
    {
        ConnectionFactory connFactory = new ConnectionFactory();
        connFactory.setUri(mUri);
        mConnection = connFactory.newConnection();
        setup();
    }
    
    protected CommandLine parseArguments(String[] args) throws ParseException
    {
        CommandLineParser parser = new GnuParser();	
        CommandLine line = parser.parse( getOptions(), args );
        setUri(mOptionUri.getValue(mUri));
        return line;
    }
    
    protected void setup() throws IOException
    {
    }
    
    protected void start() throws IOException
    {
    }
    
    protected void end() throws IOException
    {
	    mConnection.close();
    }
    
    public void setUri(String uri)
    {
    	mUri = uri;
    }
    
    protected Connection getConnection()
    {
    	return mConnection;
    }
    
    protected String getUri()
    {
    	return mUri;
    }
    
    protected Options getOptions()
    {
    	Options options = new Options();
    	options.addOption(mOptionUri);
    	return options;
    }
}

package edu.upc.ichnaea.amqp.app;

import java.io.IOException;
import java.net.URISyntaxException;
import java.security.KeyManagementException;
import java.security.NoSuchAlgorithmException;

import com.rabbitmq.client.Connection;
import com.rabbitmq.client.ConnectionFactory;

import edu.upc.ichnaea.amqp.cli.ArgumentsParser;
import edu.upc.ichnaea.amqp.cli.OptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;

public abstract class App
{
	protected Connection mConnection;
	protected String mUri;

    public static void main(String[] args, App app) {
    	try {
    		app.parseArguments(args);
	        app.connect();
	        app.start();
	        app.end();
    	} catch (OptionException e) {
    		System.err.println("Could not parse arguments: " + e);		
            System.exit(1);
    	} catch (Exception e) {
            System.err.println("Main thread caught exception: " + e);
            e.printStackTrace();
            System.exit(1);
        }   	
    }
    
    protected void connect()
    	throws KeyManagementException, NoSuchAlgorithmException, URISyntaxException, IOException {
        ConnectionFactory connFactory = new ConnectionFactory();
        connFactory.setUri(mUri);
        mConnection = connFactory.newConnection();
        setup();
    }
    
    protected void parseArguments(String[] args) throws OptionException {
        new ArgumentsParser().parse(args);
    }
    
    protected void setup() throws IOException {
    }
    
    protected void start() throws IOException {
    }
    
    protected void end() throws IOException {
	    mConnection.close();
    }
    
    public void setUriOption(String uri) {
    	mUri = uri;
    }
    
    protected Connection getConnection() {
    	return mConnection;
    }
    
    protected String getUri() {
    	return mUri;
    }
    
    protected Options getOptions() {
    	Options options = new Options();
    	options.add(new StringOption("uri") {
			@Override
			public void setValue(String value) throws OptionException {
				mUri = value;
			}
		}.setRequired(true).setDescription("The uri of the amqp server."));
    	return options;
    }
}

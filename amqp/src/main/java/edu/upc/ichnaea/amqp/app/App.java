package edu.upc.ichnaea.amqp.app;

import java.io.IOException;
import java.net.URISyntaxException;
import java.security.KeyManagementException;
import java.security.NoSuchAlgorithmException;
import java.util.logging.Logger;

import com.rabbitmq.client.Connection;
import com.rabbitmq.client.ConnectionFactory;

import edu.upc.ichnaea.amqp.cli.OptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;
import edu.upc.ichnaea.amqp.client.ClientInterface;

public abstract class App
{
	protected Connection mConnection;
	protected String mUri = "amqp://localhost";
	protected static Logger LOGGER = Logger.getLogger(App.class.getName());

    public static void main(String[] args, App app) {
    	try {
    		app.parseArguments(args);
    		app.connect();
	        app.setup();    		
	        app.start();
	        app.end();
    	} catch (OptionException e) {
    		getLogger().severe("Could not parse arguments: " + e.getMessage());
    		app.printHelp();
            System.exit(1);
    	} catch (Exception e) {
    		throw new RuntimeException(e);
        }	
    }
    
    public static Logger getLogger() {
    	return LOGGER;
    }
    
    protected void connect()
    	throws KeyManagementException, NoSuchAlgorithmException, URISyntaxException, IOException {
    	getLogger().info("connecting...");
    	ConnectionFactory connFactory = new ConnectionFactory();
        connFactory.setUri(mUri);
        mConnection = connFactory.newConnection();
    }
    
    protected void parseArguments(String[] args) throws OptionException {
       getOptions().parse(args);
    }
    
    protected void printHelp() {
    	getOptions().printHelp();
    }
    
    protected void setup() throws IOException {
    	getLogger().info("setting up app...");
    }
    
    protected void start() throws IOException {
    	getLogger().info("starting app...");
    }
    
    protected void end() throws IOException {
    	getLogger().info("ending app...");
	    mConnection.close();
    }
    
    protected Connection getConnection() {
    	return mConnection;
    }
    
    protected String getUri() {
    	return mUri;
    }
    
    protected Options getOptions() {
    	final Options options = new Options("ichnaea-amqp");
    	options.add(new StringOption("uri") {
			@Override
			public void setValue(String value) {
				mUri = value;
			}
		}.setDefaultValue(mUri).setDescription("The uri of the amqp server."));
    	return options;
    }
    
    protected void runClient(ClientInterface client) throws IOException {
		try {
			client.run();
			while(!client.hasFinished()){
				// wait forever
				Thread.sleep(1000);
			}			
		} catch (InterruptedException e) {
		}    	
    }
}

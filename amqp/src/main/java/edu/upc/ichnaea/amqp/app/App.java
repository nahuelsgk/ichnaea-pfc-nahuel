package edu.upc.ichnaea.amqp.app;

import java.io.IOException;
import java.net.URISyntaxException;
import java.security.KeyManagementException;
import java.security.NoSuchAlgorithmException;
import java.util.Arrays;
import java.util.List;

import com.rabbitmq.client.Connection;
import com.rabbitmq.client.ConnectionFactory;

public abstract class App
{
	protected Connection mConnection;
	protected String mUri;

	protected String getDefaultServerUri()
	{
		return "amqp://localhost";
	}

    public static void main(String[] args, App app)
    {
    	try {
    		app.setArguments(Arrays.asList(args));
	        app.connect();
	        app.start();
	        app.end();
        } catch (Exception ex) {
            System.err.println("Main thread caught exception: " + ex);
            ex.printStackTrace();
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
    
    public void setArguments(List<String> args)
    {
    	String uri = getDefaultServerUri();
    	if(args.size() > 0){
    		uri = args.get(0);
    		args.remove(0);
    	}
        setUri(uri);
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
}

package edu.upc.ichnaea.shell;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.Map;

import com.jcraft.jsch.ChannelExec;
import com.jcraft.jsch.JSch;
import com.jcraft.jsch.JSchException;
import com.jcraft.jsch.Session;

public class SecureShell implements ShellInterface {

	final static String DEFAULT_HOST = "localhost";	
	final static int DEFAULT_PORT = 22;
	final static String DEFAULT_PASSWORD = null;
	final static String DEFAULT_USER = null;
	
	protected String mHost;
	protected String mUser;
	protected String mPassword;
	protected int mPort;
	
	static public SecureShell create(Map<String, String> options) throws MalformedURLException
	{
		if(options.containsKey("url")) {
			return new SecureShell(options.get("url"));
		}
		int port = DEFAULT_PORT;
		String pass = DEFAULT_PASSWORD;
		String user = DEFAULT_USER;
		String host = DEFAULT_HOST;
		if(options.containsKey("port")) {
			port = Integer.parseInt(options.get("port"));
		}
		if(options.containsKey("password")) {
			pass = options.get("password");
		}
		if(options.containsKey("user")) {
			user = options.get("user");
		}
		if(options.containsKey("host")) {
			host = options.get("host");
		}
		return new SecureShell(host, user, pass, port);		
	}
	
	public SecureShell(String urlString) throws MalformedURLException
	{
		URL url = new URL(urlString);
		mHost = url.getHost();
		mPort = url.getPort();
		String[] userInfo = url.getUserInfo().split(":");
		mUser = userInfo[0];
		if(userInfo.length > 1)
		{
			mPassword = userInfo[1];
		}
	}	
	
	public SecureShell(String host, String user, String password, int port)
	{
		mHost = host;
		mUser = user;
		mPassword = password;
		mPort = port;
	}
	
	public SecureShell()
	{
		this(DEFAULT_HOST, DEFAULT_USER, DEFAULT_PASSWORD, DEFAULT_PORT);
	}
	
	public SecureShell(String host, String user)
	{
		this(host, user, DEFAULT_PASSWORD, DEFAULT_PORT);
	}
	
	public SecureShell(String host, String user, String password)
	{
		this(host, user, password, DEFAULT_PORT);
	}	
	
	@Override
	public CommandResult run(CommandInterface command) throws IOException, InterruptedException {
		JSch jsch = new JSch();
		CommandResult result;
		try {
			Session session = jsch.getSession(mUser, mHost, 22);
			if(mPassword != null){
				session.setPassword(mPassword);
			}
			ChannelExec channel = (ChannelExec) session.openChannel("exec");
			channel.setCommand(command.toString());
			channel.connect();
			command.beforeRun(this);
			result = readChannelInput(channel);
			channel.disconnect();
		    session.disconnect();
		} catch (JSchException e) {
			throw new IOException(e.getMessage());
		}
		return result;
	}
	
	protected CommandResult readChannelInput(ChannelExec channel) throws IOException
	{
		InputStream in = channel.getInputStream();
		InputStream inerr = channel.getErrStream();
	    String out = "";
	    String outerr = "";

	    while(true){
	    	out += Shell.readInputStream(in);
	    	outerr += Shell.readInputStream(inerr);
			if(channel.isClosed()){
			    return new CommandResult(out, outerr, channel.getExitStatus());
			}
			try{
				Thread.sleep(1000);
			}catch(Exception ee){
			}
	    }
	}

	@Override
	public FileInputStream readFile(String path) throws FileNotFoundException {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public FileOutputStream writeFile(String path) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public File createTempFile() throws IOException {
		// TODO Auto-generated method stub
		return null;
	}
	

}

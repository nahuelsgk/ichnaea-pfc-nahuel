package edu.upc.ichnaea.shell;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.Map;

import com.jcraft.jsch.ChannelSftp;
import com.jcraft.jsch.ChannelExec;
import com.jcraft.jsch.JSch;
import com.jcraft.jsch.JSchException;
import com.jcraft.jsch.Session;
import com.jcraft.jsch.SftpException;

public class SecureShell implements ShellInterface {

	final static String DEFAULT_HOST = "localhost";	
	final static int DEFAULT_PORT = 22;
	final static String DEFAULT_PASSWORD = null;
	final static String DEFAULT_USER = null;
	final static boolean DEFAULT_HOST_KEY_CHECKING = false;
	
	String mHost;
	int mPort;
	String mUser;
	String mPassword;
	Session mSession;
	boolean mHostKeyChecking;
	
	static public SecureShell create(Map<String, String> options) throws MalformedURLException
	{
		boolean checkHostKey = DEFAULT_HOST_KEY_CHECKING;
		if(options.containsKey("trust_host_key")) {
			checkHostKey = false;
		}		
		if(options.containsKey("url")) {
			return new SecureShell(options.get("url"), checkHostKey);
		}
		int port = DEFAULT_PORT;
		String pass = DEFAULT_PASSWORD;
		String user = DEFAULT_USER;
		String host = DEFAULT_HOST;

		if(options.containsKey("host")) {
			host = options.get("host");
		}		
		if(options.containsKey("port")) {
			port = Integer.parseInt(options.get("port"));
		}
		if(options.containsKey("password")) {
			pass = options.get("password");
		}
		if(options.containsKey("user")) {
			user = options.get("user");
		}		
		return new SecureShell(host, user, pass, port, checkHostKey);		
	}
	
	public SecureShell(String urlString) throws MalformedURLException
	{
		this(urlString, DEFAULT_HOST_KEY_CHECKING);
	}
	
	public SecureShell(String urlString, boolean checkHostKey) throws MalformedURLException
	{
		urlString = urlString.replaceAll("^[^:]+://", "");
		URL url = new URL("http://"+urlString);
		String[] userInfo = new String[2];
		if(url.getUserInfo() != null)
		{
			userInfo = url.getUserInfo().split(":");
		}
		init(url.getHost(), userInfo[0], userInfo[1], url.getPort(), checkHostKey);
	}
	
	public SecureShell(String host, String user, String password, int port, boolean checkHostKey)
	{
		init(host, user, password, port, checkHostKey);
	}
	
	public void init(String host, String user, String password, int port, boolean checkHostKey)
	{
		if(host.isEmpty())
		{
			host = DEFAULT_HOST;
		}
		if(port == -1)
		{
			port = DEFAULT_PORT;
		}		
		mHost = host;
		mPort = port;
		mUser = user;
		mPassword = password;
		mHostKeyChecking = checkHostKey;
	}
	
	public void open() throws IOException
	{
		try{
			close();
			JSch jsch = new JSch();
			mSession = jsch.getSession(mUser, mHost, mPort);
			if(mPassword != null){
				mSession.setPassword(mPassword);
			}
			
			if(!mHostKeyChecking)
			{
				java.util.Properties config = new java.util.Properties(); 
				config.put("StrictHostKeyChecking", "no");
				mSession.setConfig(config);
			}
			
			mSession.connect();
		}catch(JSchException e){
			throw new IOException(e);
		}
	}
	
	public void close()
	{
		if(mSession != null)
		{
		    mSession.disconnect();			
		}
	}
	
	@Override
	public CommandResult run(CommandInterface command) throws IOException {
		try{
			ChannelExec channel = (ChannelExec) mSession.openChannel("exec");		
			channel.setCommand(command.toString());
			command.beforeRun(this);
			CommandResult result = readChannelInput(channel);
			channel.disconnect();
			return result;
		}catch(JSchException e){
			throw new IOException(e);
		}
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
	public InputStream readFile(String path) throws IOException, FileNotFoundException {
		try{
			ChannelSftp channel = (ChannelSftp) mSession.openChannel("sftp");
		    return channel.get(path);
		}catch(SftpException e){
			throw new IOException(e);
		}catch(JSchException e){
			throw new IOException(e);
		}
	}

	@Override
	public OutputStream writeFile(String path) throws IOException {
		try{
			ChannelSftp channel = (ChannelSftp) mSession.openChannel("sftp");
		    return channel.put(path);
		}catch(SftpException e){
			throw new IOException(e);
		}catch(JSchException e){
			throw new IOException(e);
		}		    
	}
	

	@Override
	public void removeFile(String path) throws IOException {
		try{
			ChannelSftp channel = (ChannelSftp) mSession.openChannel("sftp");
		    channel.rm(path);
		}catch(SftpException e){
			throw new IOException(e);
		}catch(JSchException e){
			throw new IOException(e);
		}	
	}	

	@Override
	public String getTempPath() {
		return "/tmp";
	}
	

}

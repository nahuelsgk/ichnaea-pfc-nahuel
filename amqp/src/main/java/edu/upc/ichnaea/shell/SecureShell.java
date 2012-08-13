package edu.upc.ichnaea.shell;

import java.io.IOException;
import java.io.InputStream;

import com.jcraft.jsch.ChannelExec;
import com.jcraft.jsch.JSch;
import com.jcraft.jsch.JSchException;
import com.jcraft.jsch.Session;

public class SecureShell implements ShellInterface {

	protected String mHost;
	protected String mUser;
	protected String mPassword;
	protected int mPort;
	
	public SecureShell(String host, String user, String password, int port)
	{
		mHost = host;
		mUser = user;
		mPassword = password;
		mPort = port;
	}
	
	public SecureShell()
	{
		this("localhost", null, null, 22);
	}
	
	public SecureShell(String host, String user)
	{
		this(host, user, null, 22);
	}
	
	@Override
	public CommandResult run(Command command) throws IOException, InterruptedException {
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
	

}

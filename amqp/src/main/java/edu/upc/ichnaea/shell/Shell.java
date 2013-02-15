package edu.upc.ichnaea.shell;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.logging.Logger;

public class Shell implements ShellInterface {
	
	protected Logger mLogger = Logger.getLogger(Shell.class.getName());
	
	public Logger getLogger() {
		return mLogger;
	}	

	@Override
	public CommandResult run(CommandInterface command) throws IOException, InterruptedException {
		Runtime run = Runtime.getRuntime();
		getLogger().info("running command :"+command.toString());
		Process pr= run.exec(command.toString());
		pr.waitFor();
		String out = readInputStream(pr.getInputStream());
		String err = readInputStream(pr.getErrorStream());
		return new CommandResult(out, err, pr.exitValue());
	}
	
	public static String readInputStream(InputStream in) throws IOException {
		String out = "";
		byte[] tmp = new byte[1024];
		
		while(in.available()>0) {
			int i = in.read(tmp, 0, tmp.length);
			if(i<0){
				break;
			}
			out += new String(tmp, 0, i);
		}
		
		return out;
	}

	@Override
	public FileInputStream readFile(String path) throws FileNotFoundException {
		return new FileInputStream(path);
	}

	@Override
	public FileOutputStream writeFile(String path) throws IOException {
		try {
			return new FileOutputStream(path);
		} catch (FileNotFoundException e) {
			throw new IOException("Path is a directory.");
		}
	}
	
	public void removeFile(String path) throws IOException {
		if(!new File(path).delete())
		{
			throw new IOException("could not delete path");
		}
	}

	@Override
	public String getTempPath() throws IOException {
		return System.getProperty("java.io.tmpdir"); 
	}

	@Override
	public void open() throws IOException {

	}

	@Override
	public void close() throws IOException {
		
	}

}

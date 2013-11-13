package edu.upc.ichnaea.shell;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.logging.Logger;

public class Shell implements ShellInterface {
	
	public static class CommandResult implements CommandResultInterface
	{
		private Process mProcess;
		
		public CommandResult(Process pr) {
			mProcess = pr;
		}

		@Override
		public InputStream getInputStream() {
			return mProcess.getInputStream();
		}

		@Override
		public InputStream getErrorStream() {
			return mProcess.getErrorStream();
		}

		@Override
		public int getExitStatus() {
			return mProcess.exitValue();
		}

		@Override
		public void close() {
			try {
				mProcess.waitFor();
			} catch (InterruptedException e) {
			}	
		}
	};
	
	protected Logger mLogger = Logger.getLogger(Shell.class.getName());
	
	public Logger getLogger() {
		return mLogger;
	}	

	@Override
	public CommandResult run(CommandInterface command) throws IOException, InterruptedException {
		Runtime run = Runtime.getRuntime();
		command.beforeRun(this);
		Process pr = run.exec(command.toString());
		return new CommandResult(pr);
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

	public void createFolder(String path) throws IOException {
		new File(path).mkdirs();
	}

}

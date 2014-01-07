package edu.upc.ichnaea.shell;

import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.logging.Logger;

public class LocalShell implements ShellInterface {
	
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
			try {
				return mProcess.exitValue();
		    } catch (IllegalThreadStateException itse) {
		        return -1;
		    }
		}
		
		@Override
		public boolean finished() {
			try {
				mProcess.exitValue();
		    } catch (IllegalThreadStateException itse) {
		        return false;
		    }
		    return true;
		}

		@Override
		public void close() {
			try {
				mProcess.waitFor();
			} catch (InterruptedException e) {
			}
		}
	};
	
	public static class CommandErrorResult implements CommandResultInterface
	{
		private InputStream mErrorStream;
		private InputStream mInputStream;
		private int mExitStatus;
		
		public CommandErrorResult(Exception e, int status) {
			mErrorStream = new ByteArrayInputStream(e.getLocalizedMessage().getBytes());
			mInputStream = new ByteArrayInputStream(new byte[0]);
			mExitStatus = status;
		}

		@Override
		public InputStream getInputStream() {
			return mInputStream;
		}

		@Override
		public InputStream getErrorStream() {
			return mErrorStream;
		}

		@Override
		public int getExitStatus() {
			return mExitStatus;
		}
		
		@Override
		public boolean finished() {
		    return true;
		}

		@Override
		public void close() {
		}
	};
	
	protected Logger mLogger = Logger.getLogger(LocalShell.class.getName());
	
	public Logger getLogger() {
		return mLogger;
	}	

	@Override
	public CommandResultInterface run(CommandInterface command) throws IOException, InterruptedException {
		Runtime run = Runtime.getRuntime();
		command.beforeRun(this);
		try {
			Process pr = run.exec(command.toString());
			return new CommandResult(pr);
		} catch(IOException e) {
			return new CommandErrorResult(e,-1);
		}
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
	
	private void removeFile(File file) throws IOException {
		if(file.exists()) {
	        File[] files = file.listFiles();
	        if(null != files) {
	            for(int i=0; i<files.length; i++) {
	                removeFile(files[i]);
	            }
	        }
			if(!file.delete()) {
				throw new IOException("could not delete path");
			}
	    }
	}
	
	public void removePath(String path) throws IOException {
		removeFile(new File(path));
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

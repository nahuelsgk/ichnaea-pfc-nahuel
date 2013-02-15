package edu.upc.ichnaea.shell;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

public interface ShellInterface {
	
	public void open() throws IOException;
	public void close() throws IOException;
	
	public CommandResult run(CommandInterface command) throws IOException, InterruptedException;
	public InputStream readFile(String path) throws IOException, FileNotFoundException;
	public OutputStream writeFile(String path) throws IOException;
	public void removeFile(String path) throws IOException;
	public String getTempPath() throws IOException;
}

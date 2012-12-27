package edu.upc.ichnaea.shell;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

public interface ShellInterface {
	public CommandResult run(CommandInterface command) throws IOException, InterruptedException;
	public InputStream readFile(String path) throws FileNotFoundException;
	public OutputStream writeFile(String path);
	public File createTempFile() throws IOException;
}

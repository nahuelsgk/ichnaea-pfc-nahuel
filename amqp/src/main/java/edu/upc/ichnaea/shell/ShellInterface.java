package edu.upc.ichnaea.shell;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;

public interface ShellInterface {
	public CommandResult run(CommandInterface command) throws IOException, InterruptedException;
	public FileInputStream readFile(String path) throws FileNotFoundException;
	public FileOutputStream writeFile(String path);
	public File createTempFile() throws IOException;
}

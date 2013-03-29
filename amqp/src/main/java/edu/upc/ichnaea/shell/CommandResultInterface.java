package edu.upc.ichnaea.shell;

import java.io.IOException;
import java.io.InputStream;

public interface CommandResultInterface {
	public InputStream getInputStream() throws IOException;
	public InputStream getErrorStream() throws IOException;
	public int getExitStatus();
	public void close();
}

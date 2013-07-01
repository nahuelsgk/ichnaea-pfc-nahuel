package edu.upc.ichnaea.shell;

import java.io.IOException;

public interface ShellInterface {
	public CommandResult run(Command command) throws IOException, InterruptedException;
}

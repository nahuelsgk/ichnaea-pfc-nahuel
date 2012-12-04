package edu.upc.ichnaea.shell;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;

public class Shell implements ShellInterface {

	@Override
	public CommandResult run(CommandInterface command) throws IOException, InterruptedException {

		Runtime run = Runtime.getRuntime();
		Process pr= run.exec(command.toString());
		pr.waitFor();
		String out = readInputStream(pr.getInputStream());
		String err = readInputStream(pr.getErrorStream());
		return new CommandResult(out, err, pr.exitValue());
	}
	
	public static String readInputStream(InputStream in) throws IOException
	{
		String out = "";
		byte[] tmp = new byte[1024];
		
		while(in.available()>0){
			int i = in.read(tmp, 0, tmp.length);
			if(i<0){
				break;
			}
			out += new String(tmp, 0, i);
		}
		
		return out;
	}

	@Override
	public FileInputStream readFile(String path) throws FileNotFoundException{
		return new FileInputStream(path);
	}

	@Override
	public FileOutputStream writeFile(String path) {
		try {
			return new FileOutputStream(path);
		} catch (FileNotFoundException e) {
			throw new SecurityException("Path is a directory.");
		}
	}

}

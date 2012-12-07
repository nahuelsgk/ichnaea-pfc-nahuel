package edu.upc.ichnaea.amqp.cli;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;

public abstract class ReadFileOption extends StringOption {

	public ReadFileOption(String arg) {
		super(arg);
	}

	@Override
	public void setValue(String value) throws OptionException {
		File f = new File(value);
		if(!f.isFile()) {
			throw new OptionException("Could not find file");
		}
		if(!f.canRead()) {
			throw new OptionException("Could not read file");
		}
		try {
			setValue(new FileInputStream(f));
		} catch (FileNotFoundException e) {
		}
	}
	
	public abstract void setValue(FileInputStream value) throws OptionException;

}

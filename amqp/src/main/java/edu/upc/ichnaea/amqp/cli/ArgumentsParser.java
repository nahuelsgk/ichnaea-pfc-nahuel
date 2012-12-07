package edu.upc.ichnaea.amqp.cli;

import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.CommandLineParser;
import org.apache.commons.cli.GnuParser;
import org.apache.commons.cli.ParseException;

public class ArgumentsParser {
	
	Options mOptions = new Options();

	public void addOption(Option option) {
		mOptions.add(option);
	}
	
	public void parse(String[] args) throws OptionException {
        CommandLineParser parser = new GnuParser();	
        try {
			CommandLine line = parser.parse( mOptions.getInternalOptions(), args );
			for(Option opt : mOptions) {
				if(opt.isRequired() && !opt.inCommandLine(line)) {
					throw new OptionException("Required option \""+opt.getName()+"\" not passed.");
				}
				opt.load(line);
			}
		} catch (ParseException e) {
			throw new OptionException(e);
		}
	}
}

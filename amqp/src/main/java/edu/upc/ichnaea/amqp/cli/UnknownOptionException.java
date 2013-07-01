package edu.upc.ichnaea.amqp.cli;

public class UnknownOptionException extends OptionException {

	private static final long serialVersionUID = -105877649087753260L;
	
	public UnknownOptionException(String option) {
		super("Unknown option \""+option+"\".");
	}

}

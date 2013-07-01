package edu.upc.ichnaea.amqp.cli;

public class InvalidOptionException extends OptionException {

	private static final long serialVersionUID = 8560929124558980363L;

	public InvalidOptionException(String msg) {
		super(msg);
	}

}

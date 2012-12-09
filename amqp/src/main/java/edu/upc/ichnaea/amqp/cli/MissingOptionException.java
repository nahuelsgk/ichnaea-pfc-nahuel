package edu.upc.ichnaea.amqp.cli;

public class MissingOptionException extends OptionException {

	private static final long serialVersionUID = 4609489855313478499L;
	
	private Option mOption;

	public MissingOptionException(Option opt) {
		super("Required option \""+opt.getName()+"\" not passed.");
		mOption = opt;		
	}
	
	public Option getOption() {
		return mOption;
	}

}

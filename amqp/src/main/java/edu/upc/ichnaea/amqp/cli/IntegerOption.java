package edu.upc.ichnaea.amqp.cli;

import org.apache.commons.cli.CommandLine;

public abstract class IntegerOption extends Option {

    private int mDefault;

    public IntegerOption(String arg) {
        super(arg, true);
    }

    public IntegerOption setDefaultValue(int def) {
        mDefault = def;
        return this;
    }

    @Override
    void load(CommandLine line) throws InvalidOptionException {
        int value = mDefault;
        if (inCommandLine(line)) {
            value = Integer.parseInt(getCommandLineValue(line));
        }
        setValue(value);
    }

    abstract public void setValue(int value) throws InvalidOptionException;
}

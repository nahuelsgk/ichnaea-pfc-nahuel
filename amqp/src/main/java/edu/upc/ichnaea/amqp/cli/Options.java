package edu.upc.ichnaea.amqp.cli;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.CommandLineParser;
import org.apache.commons.cli.HelpFormatter;
import org.apache.commons.cli.ParseException;
import org.apache.commons.cli.PosixParser;
import org.apache.commons.cli.UnrecognizedOptionException;

public class Options implements Iterable<Option> {

    private org.apache.commons.cli.Options mInternalOptions = new org.apache.commons.cli.Options();
    private List<Option> mOptions = new ArrayList<Option>();
    private String mExecutable;

    public Options(String exec) {
        mExecutable = exec;
        add(new BooleanOption("help") {
            @Override
            public void setValue(boolean value) throws InvalidOptionException {
                if (value) {
                    printHelp();
                    System.exit(0);
                }
            }
        }.setDescription("Show this help message."));
    }

    public void add(Option opt) {
        if (!opt.hasChar()) {
            for (char ch : opt.getSuggestedChars()) {
                if (!hasChar(ch)) {
                    opt.setChar(ch);
                }
            }
        }
        mInternalOptions.addOption(opt.getInternalOption());
        mOptions.add(opt);
    }

    public boolean hasChar(char ch) {
        for (Option opt : this) {
            if (opt.getChar() == ch) {
                return true;
            }
        }
        return false;
    }

    public void printHelp() {
        HelpFormatter formatter = new HelpFormatter();
        formatter.printHelp(mExecutable, mInternalOptions);
    }

    org.apache.commons.cli.Options getInternalOptions() {
        return mInternalOptions;
    }

    public Iterator<Option> iterator() {
        return mOptions.iterator();
    }

    public void parse(String[] args) throws OptionException {
        try {
            CommandLineParser parser = new PosixParser();
            CommandLine line = parser.parse(mInternalOptions, args);
            for (Option opt : mOptions) {
                if (opt.isRequired() && !opt.inCommandLine(line)) {
                    throw new MissingOptionException(opt);
                }
                opt.load(line);
            }
        } catch (UnrecognizedOptionException e) {
            throw new UnknownOptionException(e.getOption());
        } catch (ParseException e) {
            throw new OptionException(e.getMessage());
        }
    }

}

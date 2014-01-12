package edu.upc.ichnaea.amqp.cli;

import java.io.File;
import java.io.FileOutputStream;
import java.io.FileNotFoundException;

public abstract class WriteFileOption extends StringOption {

    public WriteFileOption(String arg) {
        super(arg);
    }

    private boolean canWrite(File f) {
        if (f.exists()) {
            return f.canWrite();
        } else {
            return canWrite(f.getParentFile());
        }
    }

    @Override
    public void setValue(String value) throws InvalidOptionException {
        File f = new File(value);
        if (!canWrite(f)) {
            throw new InvalidOptionException("Could not write file");
        }
        try {
            setValue(new FileOutputStream(f));
        } catch (FileNotFoundException e) {
        }
    }

    public abstract void setValue(FileOutputStream value)
            throws InvalidOptionException;

}

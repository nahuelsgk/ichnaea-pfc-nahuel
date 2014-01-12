package edu.upc.ichnaea.amqp.cli;

import java.io.IOException;

public class OptionException extends IOException {

    private static final long serialVersionUID = -5275496819732986851L;

    public OptionException(String msg) {
        super(msg);
    }
}

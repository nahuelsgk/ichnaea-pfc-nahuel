 
package edu.upc.ichnaea.shell;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.BufferedReader;

import java.util.logging.Logger;

import edu.upc.ichnaea.amqp.IOUtils;

public abstract class CommandReader {

    CommandResultInterface mResult;
    boolean mVerbose;

    public CommandReader(CommandResultInterface result, boolean verbose) {
       mResult = result;
       mVerbose = verbose;
    }

    public void read() throws IOException {
        BufferedReader in = new BufferedReader(new InputStreamReader(mResult.getInputStream()));
        String line;

        while (!mResult.finished()) {
            line = in.readLine();
            if (line == null) {
                break;
            }
            if(mVerbose) {
                getLogger().info(line);
            }
            onLineRead(line);
        }
        mResult.close();

        int exitStatus = mResult.getExitStatus();
        String error = null;
        if (exitStatus != 0) {
            InputStream es = mResult.getErrorStream();
            if (es.available() > 0) {
                error = new String(IOUtils.read(es));
            } else {
                error = "Got command exit status " + exitStatus;
            }
        }

        if (error != null) {
            throw new IOException(error);
        } 
    }

    private static Logger LOGGER = Logger.getLogger(CommandReader.class.getName());

    public static Logger getLogger() {
        return LOGGER;
    }

    protected abstract void onLineRead(String line);

};

    
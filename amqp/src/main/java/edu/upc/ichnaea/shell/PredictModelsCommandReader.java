 
package edu.upc.ichnaea.shell;

import java.io.IOException;

import edu.upc.ichnaea.amqp.model.PredictModelsResult;

import java.util.Calendar;

public abstract class PredictModelsCommandReader extends CommandReader {

    public PredictModelsCommandReader(CommandResultInterface result, boolean verbose) {
        super(result, verbose);
    }


    protected void onLineRead(String line) {
        
    }

    protected abstract void onUpdate(float percent, Calendar end, PredictModelsResult result);
};

    
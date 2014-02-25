package edu.upc.ichnaea.shell;

import java.io.IOException;

import edu.upc.ichnaea.amqp.FileUtils;

public class PredictModelsCommand extends IchnaeaCommand {

    private String mDatasetPath;
    private String mOutputPath;
    private boolean mVerbose;

    public PredictModelsCommand(String datasetPath, boolean verbose) {
        mDatasetPath = datasetPath;
        mVerbose = verbose;
    }
    
    @Override
    public void beforeRun(ShellInterface shell) {
        try {
            mOutputPath = FileUtils.tempPath(shell.getTempPath());
        } catch (IOException e) {
        }
    }

    public String getOutputPath() {
        return mOutputPath;
    }

    public String getParameters() {
        String params = "";

        if (mOutputPath != null) {
            params += " --output=" + mOutputPath + "";
        }
        if(mVerbose) {
            params += " --verbose";
        }
        params += " " + mDatasetPath;
        return params;
    }

}

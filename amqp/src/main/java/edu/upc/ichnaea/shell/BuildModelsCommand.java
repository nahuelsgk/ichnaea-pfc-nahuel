package edu.upc.ichnaea.shell;

import java.io.IOException;

import edu.upc.ichnaea.amqp.FileUtils;

public class BuildModelsCommand extends IchnaeaCommand {

    private String mAgingPath;
    private String mDatasetPath;
    private String mOutputPath;
    private boolean mVerbose;

    public BuildModelsCommand(String datasetPath, String agingPath, boolean verbose) {
        mDatasetPath = datasetPath;
        mAgingPath = agingPath;
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
        String params = "build";

        if (mOutputPath != null) {
            params += " --output=" + mOutputPath + "";
        }
        params += " --aging=" + mAgingPath + "";
        if(mVerbose) {
            params += " --verbose";
        }
        params += " " + mDatasetPath;
        return params;
    }

}

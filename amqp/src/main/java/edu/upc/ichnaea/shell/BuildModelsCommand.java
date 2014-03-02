package edu.upc.ichnaea.shell;

import java.io.IOException;

import edu.upc.ichnaea.amqp.FileUtils;

public class BuildModelsCommand extends IchnaeaCommand {

    private String mAgingPath;
    private String mDatasetPath;
    private String mModelsPath;
    private boolean mVerbose;

    public BuildModelsCommand(String datasetPath, String agingPath, boolean verbose) {
        mDatasetPath = datasetPath;
        mAgingPath = agingPath;
        mVerbose = verbose;
    }
    
    @Override
    public void beforeRun(ShellInterface shell) {
        try {
            mModelsPath = FileUtils.tempPath(shell.getTempPath());
        } catch (IOException e) {
        }
    }

    public String getModelsPath() {
        return mModelsPath;
    }

    public String getParameters() {
        String params = "build";

        if (mModelsPath != null) {
            params += " --models=" + mModelsPath + "";
        }
        params += " --aging=" + mAgingPath + "";
        if(mVerbose) {
            params += " --verbose";
        }
        params += " " + mDatasetPath;
        return params;
    }

}

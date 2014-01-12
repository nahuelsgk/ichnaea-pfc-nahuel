package edu.upc.ichnaea.shell;

import java.io.IOException;

import edu.upc.ichnaea.amqp.FileUtils;

public class BuildModelsCommand extends IchnaeaCommand {

    private String mAgingPath;
    private String mDatasetPath;
    private String mOutputPath;

    public BuildModelsCommand(String datasetPath, String agingPath) {
        mDatasetPath = datasetPath;
        mAgingPath = agingPath;
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
        params += " --aging=" + mAgingPath + "";
        params += " --debug";
        params += " " + mDatasetPath;
        return params;
    }

}

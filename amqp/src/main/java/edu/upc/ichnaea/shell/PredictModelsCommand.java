package edu.upc.ichnaea.shell;

public class PredictModelsCommand extends IchnaeaCommand {

    private String mDatasetPath;
    private String mModelsPath;
    private boolean mVerbose;

    public PredictModelsCommand(String datasetPath, String modelsPath, boolean verbose) {
        mDatasetPath = datasetPath;
        mModelsPath = modelsPath;
        mVerbose = verbose;
    }
    
    public String getParameters() {
        String params = "predict";
        params += " --model=" + mModelsPath + "";
        if(mVerbose) {
            params += " --verbose";
        }
        params += " " + mDatasetPath;
        return params;
    }

}

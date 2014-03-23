package edu.upc.ichnaea.amqp.model;

public class PredictModelsResult {

    protected String mName;
    protected Dataset mDataset;
    protected Dataset mConfusionMatrix;
    protected float mTestError;
    protected int mTotalSamples;
    protected int mPredictedSamples;

    public PredictModelsResult() {
        this(0,0);
    }

    public PredictModelsResult(int predictedSamples, int totalSamples) {
        mPredictedSamples = predictedSamples;
        mTotalSamples = totalSamples;
        mTestError = 0.0f;
        mDataset = new Dataset();
        mConfusionMatrix = new Dataset();
    }

    public PredictModelsResult(String name, Dataset dataset, int totalSamples, Dataset confMatrix, float testError) {
        mName = name;
        mDataset = dataset;
        mConfusionMatrix = confMatrix;
        mTotalSamples = totalSamples;
        mPredictedSamples = totalSamples;
        mTestError = testError;
    }

    public Dataset getConfusionMatrix() {
        return mConfusionMatrix;
    }

    public int getTotalSamples() {
        return mTotalSamples;
    }

    public int getPredictedSamples() {
        return mPredictedSamples;
    }

    public Dataset getDataset() {
        return mDataset;
    }

    public float getTestError() {
        return mTestError;
    }

    public boolean isEmpty() {
        return mPredictedSamples == 0;
    }

    public boolean isFinished() {
        return !isEmpty() && mPredictedSamples >= mTotalSamples && !mDataset.isEmpty();
    }

    public String getName() {
        return mName;
    }

    public String toString() {
        String str = "";

        if(!mName.isEmpty()) {
            str += "Name: "+mName+"\n";
        }

        if(mDataset != null) {
            str += "Table\n";
            str += mDataset.toString() + "\n";
        }

        str += mPredictedSamples + "/" + mTotalSamples + " samples\n";
        str += mTestError*100.0f + "% error\n";
        if(mConfusionMatrix != null) {
            str += "Confusion matrix\n";
            str += mConfusionMatrix.toString() + "\n";
        }
        return str;
    }

}

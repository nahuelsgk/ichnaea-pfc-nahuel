package edu.upc.ichnaea.amqp.model;

import java.util.Calendar;

public class PredictModelsResult {

    protected Dataset mDataset;
    protected float[][] mConfusionMatrix;
    protected int mTotalSamples;
    protected int mPredictedSamples;
    protected float mTestError;

    public PredictModelsResult(Dataset dataset, float[][] confMatrix, int totalSamples, int predictedSamples) {
        mDataset = dataset;
        mConfusionMatrix = confMatrix;
        mTotalSamples = totalSamples;
        mPredictedSamples = predictedSamples;
    }

    public float[][] getConfusionMatrix() {
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

}

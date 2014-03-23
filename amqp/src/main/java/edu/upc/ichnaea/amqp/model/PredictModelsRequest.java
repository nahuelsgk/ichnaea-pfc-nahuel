package edu.upc.ichnaea.amqp.model;

public class PredictModelsRequest {

    protected Dataset mDataset;
    protected byte[] mData;
    protected String mId;

    public PredictModelsRequest(String id, Dataset dataset, byte[] data) {
        mDataset = dataset;
        mId = id;
        mData = data;
    }

    public PredictModelsRequest(String id) {
        mId = id;
    }

    public String getId() {
        return mId;
    }

    public Dataset getDataset() {
        return mDataset;
    }
    
    public byte[] getData() {
        return mData;
    }

    public boolean isEmpty() {
        return mDataset.isEmpty();
    }
}
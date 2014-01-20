package edu.upc.ichnaea.amqp.model;

public class TestModelsRequest {

    protected Dataset mDataset;
    protected byte[] mData;
    protected String mId;

    public TestModelsRequest(String id, Dataset dataset, byte[] data) {
        mDataset = dataset;
        mId = id;
        mData = data;
    }

    public TestModelsRequest(String id) {
        mId = id;
    }

    public String getId() {
        return mId;
    }

    public Dataset getDataset() {
        return mDataset;
    }

    public boolean isEmpty() {
        return mDataset.isEmpty();
    }
}
package edu.upc.ichnaea.amqp.model;

public class BuildModelsRequest {

    protected Dataset mDataset;
    protected DatasetAging mAging;
    protected String mId;

    public BuildModelsRequest(String id, Dataset dataset, DatasetAging aging) {
        mDataset = dataset;
        mAging = aging;
        mId = id;
    }

    public BuildModelsRequest(String id) {
        mId = id;
    }

    public String getId() {
        return mId;
    }

    public Dataset getDataset() {
        return mDataset;
    }

    public DatasetAging getAging() {
        return mAging;
    }

    public boolean isEmpty() {
        return mDataset.isEmpty() && mAging.isEmpty();
    }
}
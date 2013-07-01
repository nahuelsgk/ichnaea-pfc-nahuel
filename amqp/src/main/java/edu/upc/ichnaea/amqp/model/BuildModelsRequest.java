package edu.upc.ichnaea.amqp.model;

public class BuildModelsRequest {

	protected Dataset mDataset;
	protected DatasetSeasons mSeasons;
	protected String mId;
	
	public BuildModelsRequest(String id, Dataset dataset, DatasetSeasons seasons) {
		mDataset = dataset;
		mSeasons = seasons;
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
	
	public DatasetSeasons getSeasons() {
		return mSeasons;
	}
}
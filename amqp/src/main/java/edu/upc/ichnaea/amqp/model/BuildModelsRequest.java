package edu.upc.ichnaea.amqp.model;

public class BuildModelsRequest {

	public enum Season {
		Summer, Winter
	}
	
	protected Season mSeason;
	protected Dataset mDataset;	
	
	public BuildModelsRequest(Dataset dataset, Season season) {
		mDataset = dataset;
		mSeason = season;
	}
	
	public Season getSeason() {
		return mSeason;
	}
	
	public Dataset getDataset() {
		return mDataset;
	}
}
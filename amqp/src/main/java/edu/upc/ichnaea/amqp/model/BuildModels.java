package edu.upc.ichnaea.amqp.model;

public class BuildModels {

	public enum Season {
		Summer, Winter
	}
	
	protected Season mSeason;
	protected Dataset mDataset;	
	
	public BuildModels(Dataset dataset, Season season)
	{
		mDataset = dataset;
		mSeason = season;
	}
	
	public Season getSeason()
	{
		return mSeason;
	}
	
	public Dataset getDataset()
	{
		return mDataset;
	}
}
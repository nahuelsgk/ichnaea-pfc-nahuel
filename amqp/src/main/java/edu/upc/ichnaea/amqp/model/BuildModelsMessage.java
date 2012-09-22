package edu.upc.ichnaea.amqp.model;

public class BuildModelsMessage {

	public enum Season {
		Summer, Winter
	}
	
	protected Season mSeason;
	protected ModelsDataset mDataset;	
	
	public BuildModelsMessage(ModelsDataset dataset, Season season)
	{
		mDataset = dataset;
		mSeason = season;
	}
	
	public Season getSeason()
	{
		return mSeason;
	}
	
	public ModelsDataset getDataset()
	{
		return mDataset;
	}
}

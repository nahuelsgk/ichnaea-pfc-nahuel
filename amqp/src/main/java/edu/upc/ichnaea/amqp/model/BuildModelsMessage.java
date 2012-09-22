package edu.upc.ichnaea.amqp.model;

public class BuildModelsMessage {

	public enum Season {
		Summer, Winter
	}
	
	protected Season mSeason;
	
	public BuildModelsMessage(Season season)
	{
		mSeason = season;
	}
	
	public Season getSeason()
	{
		return mSeason;
	}
}

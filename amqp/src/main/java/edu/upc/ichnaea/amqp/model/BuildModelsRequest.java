package edu.upc.ichnaea.amqp.model;

import java.util.Set;

public class BuildModelsRequest {

	public enum Season {
		Summer, Winter
	}
	
	protected Season mSeason;
	protected Dataset mDataset;
	protected int mSection;
	protected String mId;
	protected String mFake;
	
	/**
	 * list of the attributes in which log10 function must be applied before modelling
	 */
	protected Set<String> mLogColumns;
	
	/**
	 * list of all the attributes that must not be standardized
	 */
	protected Set<String> mNotStdColumns;

	/**
	 * list of all the attributes that will not be considered when computing all the combinations of a given size
	 */
	protected Set<String> mNotCombinedColumns;

	/**
	 * list of all the attributes that are directly diluted by dividing its value by the dilution degree
	 */
	protected Set<String> mDirectDilutedColumns;
	
	public BuildModelsRequest(String id, Dataset dataset, Season season, int section) {
		mDataset = dataset;
		mSeason = season;
		mSection = section;
		mId = id;
		mFake = null;
	}
	
	public BuildModelsRequest(String id, String fake) {
		mId = id;
		mFake = fake;
	}
	
	public String getId() {
		return mId;
	}
	
	public int getSection() {
		return mSection;
	}
	
	public Season getSeason() {
		return mSeason;
	}
	
	public String getFake() {
		return mFake;
	}
	
	public Dataset getDataset() {
		return mDataset;
	}
	
	public boolean isFake() {
		return mFake != null;
	}
}
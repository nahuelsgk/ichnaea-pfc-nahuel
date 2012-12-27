package edu.upc.ichnaea.amqp.model;

import java.util.Set;

public class BuildModelsRequest {

	public enum Season {
		Summer, Winter
	}
	
	protected Season mSeason;
	protected Dataset mDataset;
	protected int mSection;
	protected int mId;
	
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
	
	public BuildModelsRequest(int id, Dataset dataset, Season season, int section) {
		mDataset = dataset;
		mSeason = season;
		mSection = section;
		mId = id;
	}
	
	public int getId() {
		return mId;
	}
	
	public int getSection() {
		return mSection;
	}
	
	public Season getSeason() {
		return mSeason;
	}
	
	public Dataset getDataset() {
		return mDataset;
	}
}
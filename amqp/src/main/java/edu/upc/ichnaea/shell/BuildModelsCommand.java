package edu.upc.ichnaea.shell;

import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.model.BuildModels.Season;
import edu.upc.ichnaea.amqp.model.Dataset;

public class BuildModelsCommand extends IchnaeaCommand {

	private Season mSeason;
	private Dataset mDataset;
	private String mDatasetPath;
	
	public BuildModelsCommand(BuildModels msg) {
		this(msg.getSeason(), msg.getDataset());	
	}
	
	public BuildModelsCommand(Season season, Dataset dataset)
	{
		mSeason = season;
		mDataset = dataset;
	}
	
	public String getParameters()
	{
		String params = "";
		if(mSeason == Season.Summer){
			params += "--summer";
		}else if(mSeason == Season.Winter){
			params += "--winter";
		}
		params += " "+mDatasetPath;
		return params;
	}

}

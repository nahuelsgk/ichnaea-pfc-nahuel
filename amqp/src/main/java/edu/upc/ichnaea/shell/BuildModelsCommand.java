package edu.upc.ichnaea.shell;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest.Season;

public class BuildModelsCommand extends IchnaeaCommand {

	private Season mSeason;
	private String mDatasetPath;
	
	public BuildModelsCommand(Season season, String datasetPath) {
		mSeason = season;
		mDatasetPath = datasetPath;
	}
	
	public String getParameters() {
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

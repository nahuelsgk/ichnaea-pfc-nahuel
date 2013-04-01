package edu.upc.ichnaea.shell;

import java.io.IOException;

import edu.upc.ichnaea.amqp.FileUtils;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest.Season;

public class BuildModelsCommand extends IchnaeaCommand {

	private Season mSeason;
	private String mDatasetPath;
	private String mOutputPath;
	
	public BuildModelsCommand(Season season, String datasetPath) {
		mSeason = season;
		mDatasetPath = datasetPath;
	}
	
	public void beforeRun(ShellInterface shell) {
		try {
			mOutputPath = FileUtils.tempPath(shell.getTempPath());
		} catch (IOException e) {
		}
	}
	
	public String getOutputPath() {
		return mOutputPath;
	}
	
	public String getParameters() {
		String params = "";
		if(mSeason == Season.Summer){
			params += " --season=summer";
		}else if(mSeason == Season.Winter){
			params += " --season=winter";
		}
		params += " --fake=10:1";
		if(mOutputPath != null) {
			params += " --output=\""+mOutputPath+"\"";
		}
		params += " "+mDatasetPath;
		return params;
	}

}

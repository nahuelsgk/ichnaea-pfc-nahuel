package edu.upc.ichnaea.shell;

import java.io.FileOutputStream;
import java.io.IOException;

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
	
	public void beforeRun(ShellInterface shell)
	{
		mDatasetPath = "/tmp/lalala";
		FileOutputStream out = shell.writeFile(mDatasetPath);
		try {
			out.write(mDataset.toString().getBytes());
			out.close();
		} catch (IOException e) {
		}
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

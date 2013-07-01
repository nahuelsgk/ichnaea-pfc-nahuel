package edu.upc.ichnaea.shell;

import java.io.IOException;

import edu.upc.ichnaea.amqp.FileUtils;

public class BuildModelsCommand extends IchnaeaCommand {

	private String mDatasetPath;
	private String mOutputPath;
	
	public BuildModelsCommand(String datasetPath) {
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

		if(mOutputPath != null) {
			params += " --output=\""+mOutputPath+"\"";
		}
		params += " "+mDatasetPath;
		return params;
	}

}

 
package edu.upc.ichnaea.shell;

import edu.upc.ichnaea.amqp.model.PredictModelsResult;
import edu.upc.ichnaea.amqp.model.Dataset;

import java.util.Calendar;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public abstract class PredictModelsCommandReader extends CommandReader {

    enum DatasetType {
        Dataset,
        ConfusionMatrix,
        None
    };

	static final String mDatasetStart = "Table";
	static final String mDatasetEnd = "";
    static final String mConfMatrixStart = "Confusion matrix";
    
    protected DatasetCommandReader mDatasetReader;
    protected DatasetType mDatasetType;
    protected Pattern mRegexInfo;
    protected Pattern mRegexSample;
    protected Dataset mDataset;
    protected Dataset mConfMatrix;
    protected float mPercent;
    protected Calendar mEnd;

    public PredictModelsCommandReader(CommandResultInterface result, boolean verbose) {
        super(result, verbose);
        mRegexInfo = Pattern.compile("cyprus_test-(.+): \\d+/(\\d+), \\d+ \\d+ \\d+ \\d+, (\\d+.?\\d*)%");
        mRegexSample = Pattern.compile("\\*\\*\\*New sample, # (\\d+) / (\\d+)");
    }

    protected void onLineRead(String line) {

        if(line.startsWith(mDatasetStart)) {
            mDatasetReader = new DatasetCommandReader(mVerbose);
            mDatasetType = DatasetType.Dataset;
        } else if(line.startsWith(mConfMatrixStart)) {
            mDatasetReader = new DatasetCommandReader(mVerbose);
            mDatasetReader.setSquare(true);
            mDatasetType = DatasetType.ConfusionMatrix;
        } else if(line.equals(mDatasetEnd)) {
            if(mDatasetType == DatasetType.Dataset) {
                mDataset = mDatasetReader.getDataset();
            } else if (mDatasetType == DatasetType.ConfusionMatrix) {
                mConfMatrix = mDatasetReader.getDataset();
                getLogger().info("writing conf matrix "+mConfMatrix.toString());
            }
            mDatasetType = DatasetType.None;
            mDatasetReader = null;
        } else if(mDatasetReader != null) {
            mDatasetReader.onLineRead(line);
        } else {
        	Matcher m = mRegexSample.matcher(line);
        	if(m.find()) {
        		int predictedSamples = 0;
	        	try {
	                predictedSamples = Integer.parseInt(m.group(1));
	            } catch (NumberFormatException e) {
	            }
	        	int totalSamples = 0;
	        	try {
	                totalSamples = Integer.parseInt(m.group(2));
	            } catch (NumberFormatException e) {
	            }
	         	PredictModelsResult result = new PredictModelsResult(predictedSamples, totalSamples);
	         	getLogger().info("update sample");
	            onUpdate(mPercent, mEnd, result);
        	} else if(mDataset != null && mConfMatrix != null) {
		        m = mRegexInfo.matcher(line);
		        if (m.find()) {
                    String name = m.group(1);
		        	int totalSamples = 0;
		        	try {
		                totalSamples = Integer.parseInt(m.group(2));
		            } catch (NumberFormatException e) {
		            }
		            float testError = 0.0f;
					try {
		                testError = Float.parseFloat(m.group(3))/100.0f;
		            } catch (NumberFormatException e) {
		            }
		            PredictModelsResult result = new PredictModelsResult(name, mDataset, totalSamples, mConfMatrix, testError);
		            getLogger().info("update result");
		            onUpdate(mPercent, mEnd, result);
		        }
		    }
	    }
    }

    protected abstract void onUpdate(float percent, Calendar end, PredictModelsResult result);
};

    
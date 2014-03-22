 
package edu.upc.ichnaea.shell;

import java.io.IOException;

import edu.upc.ichnaea.amqp.model.PredictModelsResult;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

import java.util.Calendar;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public abstract class PredictModelsCommandReader extends CommandReader {

	static final String mTableStart = "Table:";
	static final String mTableEnd = "";
    
    protected Pattern mRegexInfo;
    protected Pattern mRegexSample;
    protected Dataset mDataset;
    protected boolean mInDataset;
    protected float mPercent;
    protected Calendar mEnd;
    protected int mDatasetColumnOffset;

    public PredictModelsCommandReader(CommandResultInterface result, boolean verbose) {
        super(result, verbose);
        mInDataset = false;
        mRegexInfo = Pattern.compile("cyprus_test-(.+): \\d+/(\\d+), (\\d+) (\\d+) (\\d+) (\\d+), (\\d+.?\\d*)%");
        mRegexSample = Pattern.compile("\\*\\*\\*New sample, # (\\d+) / (\\d+)");
    }

    protected boolean onDatasetLineRead(String line) {
        if(line.equals(mTableStart)) {
            mDataset = new Dataset();
            mInDataset = true;
            mDatasetColumnOffset = 0;
        }
    	else if(line.equals(mTableEnd))
    	{
    		mInDataset = false;
    	}
    	else if(mInDataset && mDataset != null)
    	{
    		boolean header = line.charAt(0) == ' ';
    		String[] cells = line.split("\\s+");
            // first element is empty
    		if(header) {
                mDatasetColumnOffset = mDataset.columns().size();
    			for(int i = 1; i < cells.length; i++) {
    				mDataset.add(new DatasetColumn(cells[i]));
    			}
    		} else {
                // first col is a counter
    			for(int i = 1; i < cells.length; i++) {
    				DatasetColumn col = mDataset.get(mDatasetColumnOffset+i-1);
    				if(col != null) {
    					col.add(cells[i]);
    				}
    			}
    		}
    	}
        return mInDataset;
    }

    protected void onLineRead(String line) {

        if(onDatasetLineRead(line))
        {
        }
        else
        {
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
        	}
        	if(mDataset != null) {
		        m = mRegexInfo.matcher(line);
		        if (m.find()) {
                    String name = m.group(1);
		        	int totalSamples = 0;
		        	try {
		                totalSamples = Integer.parseInt(m.group(2));
		            } catch (NumberFormatException e) {
		            }
		            float[][] confMatrix = new float[2][2];
					try {
		                confMatrix[0][0] = Float.parseFloat(m.group(3));
		                confMatrix[0][1] = Float.parseFloat(m.group(4));
		                confMatrix[1][0] = Float.parseFloat(m.group(5));
		                confMatrix[1][1] = Float.parseFloat(m.group(6));
		            } catch (Exception e) {
		            }
		            float testError = 0.0f;
					try {
		                testError = Float.parseFloat(m.group(6))/100.0f;
		            } catch (NumberFormatException e) {
		            }
		            PredictModelsResult result = new PredictModelsResult(name, mDataset, totalSamples, confMatrix, testError);
		            getLogger().info("update result");
		            onUpdate(mPercent, mEnd, result);
		        }
		    }
	    }
    }

    protected abstract void onUpdate(float percent, Calendar end, PredictModelsResult result);
};

    
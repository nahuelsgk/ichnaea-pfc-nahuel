package edu.upc.ichnaea.amqp.app;

import java.io.FileReader;
import java.io.IOException;

import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.Option;
import org.apache.commons.cli.Options;
import org.apache.commons.cli.ParseException;

import edu.upc.ichnaea.amqp.csv.CsvDatasetReader;
import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.model.BuildModels.Season;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.requester.BuildModelsRequester;
import edu.upc.ichnaea.amqp.requester.RequesterInterface;

public class BuildModelsRequestApp extends RequestApp {

	Option mOptionDataset = new Option("d", "dataset", true, "The file with the dataset.");
	Option mOptionDatasetFormat = new Option("f", "dataset-format", true, "The dataset format.");
	Option mOptionSeason = new Option("s", "season", true, "The season for which to build the models.");
	
	Dataset mDataset;
	Season mSeason = Season.Summer;
	
    protected Options getOptions() {
    	Options options = super.getOptions();
    	options.addOption(mOptionDataset);
    	options.addOption(mOptionDatasetFormat);
    	options.addOption(mOptionSeason);
    	return options;
    }
    
    protected void setSeasonOption(String season) {
    	mSeason = Season.valueOf(Season.class, season);
    }
    
    protected void setDatasetOption(String dataset) throws ParseException {
    	try {
			mDataset = new CsvDatasetReader().read(new FileReader(dataset));
		} catch (IOException e) {
			throw new ParseException("Could not read dataset.");
		}
    }
    
    @Override
    protected CommandLine parseArguments(String[] args) throws ParseException {
    	CommandLine line = super.parseArguments(args);
    	setDatasetOption(mOptionDataset.getValue());
    	setSeasonOption(mOptionSeason.getValue());
    	return line;
    }
	
	@Override
	protected RequesterInterface createRequester() {
		return new BuildModelsRequester(new BuildModels(mDataset, mSeason));
	}
	
  
}

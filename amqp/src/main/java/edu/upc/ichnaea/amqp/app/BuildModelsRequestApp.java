package edu.upc.ichnaea.amqp.app;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.cli.EnumOption;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.ReadFileOption;
import edu.upc.ichnaea.amqp.csv.CsvDatasetReader;
import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.model.BuildModels.Season;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.requester.BuildModelsRequester;
import edu.upc.ichnaea.amqp.requester.RequesterInterface;
import edu.upc.ichnaea.amqp.xml.XmlDatasetReader;

public class BuildModelsRequestApp extends RequestApp {

	enum Format {
		Csv,
		Xml
	}
	
	Format mDatasetFormat = Format.Csv;
	Season mSeason = Season.Summer;
	Reader mDatasetReader;
	
    public static void main(String[] args) {   	
    	main(args, new BuildModelsRequestApp());
    }	
	
    protected Options getOptions() {
    	Options options = super.getOptions();
    	options.add(new EnumOption<Season>("season") {
			@Override
			public void setValue(Season value) {
				mSeason = value;
			}
		}.setDefaultValue(mSeason).setDescription("The season for which to build the models."));
    	options.add(new ReadFileOption("dataset") {
			@Override
			public void setValue(FileInputStream value) {
				mDatasetReader = new InputStreamReader(value);
			}
		}.setRequired(true).setDescription("The file with the dataset."));
    	options.add(new EnumOption<Format>("dataset-format") {
			@Override
			public void setValue(Format value) {
				mDatasetFormat = value;
			}
		}.setDefaultValue(mDatasetFormat).setDescription("The dataset format."));    	
    	return options;
    }
	
	@Override
	protected RequesterInterface createRequester() throws IOException {
		Dataset dataset = null;
		try {
			if(mDatasetFormat == Format.Csv) {
				dataset = new CsvDatasetReader().read(mDatasetReader);
			} else if(mDatasetFormat == Format.Xml) {
				dataset = new XmlDatasetReader().read(mDatasetReader);
			}
		} catch(SAXException e) {
			throw new IOException(e);
		}
		return new BuildModelsRequester(new BuildModels(dataset, mSeason));
	}
	
  
}

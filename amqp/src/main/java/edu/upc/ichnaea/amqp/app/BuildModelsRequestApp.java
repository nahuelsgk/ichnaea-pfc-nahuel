package edu.upc.ichnaea.amqp.app;

import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.cli.EnumOption;
import edu.upc.ichnaea.amqp.cli.IntegerOption;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.ReadFileOption;
import edu.upc.ichnaea.amqp.cli.StringOption;
import edu.upc.ichnaea.amqp.cli.WriteFileOption;
import edu.upc.ichnaea.amqp.client.BuildModelsRequestClient;
import edu.upc.ichnaea.amqp.csv.CsvDatasetReader;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest.Season;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.xml.XmlDatasetReader;

public class BuildModelsRequestApp extends App {

	enum Format {
		Csv,
		Xml
	}
	
	BuildModelsRequestClient mClient;
	Format mDatasetFormat = Format.Csv;
	Season mSeason = Season.Summer;
	int mSection = 1;
	Reader mDatasetReader;
	FileOutputStream mResponseOutput;
	
	String mResponseQueue = "ichnaea.build-models.response";
	String mRequestQueue = "ichnaea.build-models.request";
	String mRequestExchange = "ichnaea.build-models.request";
	
    public static void main(String[] args) {   	
    	main(args, new BuildModelsRequestApp());
    }

	@Override
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
    	options.add(new IntegerOption("section") {
			@Override
			public void setValue(int value) {
				mSection = value;
			}
		}.setDefaultValue(mSection).setDescription("The model section to build."));
    	options.add(new WriteFileOption("output") {
			@Override
			public void setValue(FileOutputStream value) {
				mResponseOutput = value;
			}
		}.setDescription("The file to write with the response."));
    	options.add(new StringOption("request-queue"){
			@Override
			public void setValue(String value) {
				mRequestQueue = value;
			}
    	}.setDefaultValue(mRequestQueue).setDescription("The queue to send the request."));    	
    	options.add(new StringOption("request-exchange"){
			@Override
			public void setValue(String value) {
				mRequestExchange = value;
			}
    	}.setDefaultValue(mRequestExchange).setDescription("The exchange to send the request."));    	
    	options.add(new StringOption("response-queue"){
			@Override
			public void setValue(String value) {
				mResponseQueue = value;
			}
    	}.setDefaultValue(mResponseQueue).setDescription("The queue to listen for responses."));    	
    	return options;
    }

	@Override
    protected void setup() throws IOException
    {
		super.setup();
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
		String id = "java.BuildModelsRequestClient";
		BuildModelsRequest request = new BuildModelsRequest(id, dataset, mSeason, mSection);
		mClient = new BuildModelsRequestClient(request, mRequestQueue, mRequestExchange, mResponseQueue, mResponseOutput);
		mClient.setup(mConnection.createChannel());
	}
	
	@Override
	protected void start() throws IOException
	{
		super.start();
		runClient(mClient);
	}
	
}

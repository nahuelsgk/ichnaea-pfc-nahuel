package edu.upc.ichnaea.amqp.app;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.security.InvalidParameterException;
import java.util.HashMap;
import java.util.Map;

import edu.upc.ichnaea.amqp.cli.BooleanOption;
import edu.upc.ichnaea.amqp.cli.EnumOption;
import edu.upc.ichnaea.amqp.cli.InvalidOptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.ReadFileOption;
import edu.upc.ichnaea.amqp.cli.StringOption;
import edu.upc.ichnaea.amqp.cli.WriteFileOption;
import edu.upc.ichnaea.amqp.client.BuildModelsRequestClient;
import edu.upc.ichnaea.amqp.data.CsvDatasetReader;
import edu.upc.ichnaea.amqp.data.AgingFolderReader;
import edu.upc.ichnaea.amqp.model.BuildModelsFakeRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetAging;
import edu.upc.ichnaea.amqp.xml.XmlDatasetReader;

public class BuildModelsRequestApp extends App {

	enum Format {
		Csv,
		Xml
	}
	
	BuildModelsRequestClient mClient;
	String mAgingPath = "env%column%-%aging%.txt";
	String mAgingPositions = "Summer:0.5, Winter:0.0, Estiu:0.5, Hivern:0.0";
	Format mDatasetFormat = Format.Csv;
	Reader mDatasetReader;
	FileOutputStream mResponseOutput;
	
	String mResponseQueue = "ichnaea.build-models.response";
	String mRequestQueue = "ichnaea.build-models.request";
	String mRequestExchange = "ichnaea.build-models.request";
	String mFake = null;
	boolean mDebug = false;
	
    public static void main(String[] args) {   	
    	main(args, new BuildModelsRequestApp());
    }

	@Override
    protected Options getOptions() {
    	Options options = super.getOptions();
    	options.add(new ReadFileOption("dataset") {
			@Override
			public void setValue(FileInputStream value) {
				mDatasetReader = new InputStreamReader(value);
			}
		}.setDescription("The file with the dataset."));    	
    	options.add(new StringOption("aging") {
			@Override
			public void setValue(String value) {
				mAgingPath = value;
			}
		}.setDefaultValue(mAgingPath).setDescription("The path where the aging files are kept."));
    	options.add(new StringOption("aging-positions") {
			@Override
			public void setValue(String value) {
				mAgingPositions = value;
			}
		}.setDefaultValue(mAgingPositions).setDescription("The list of positions of the agings."));    	
    	options.add(new EnumOption<Format>("dataset-format") {
			@Override
			public void setValue(Format value) {
				mDatasetFormat = value;
			}
		}.setDefaultValue(mDatasetFormat).setDescription("The dataset format."));  	
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
    	options.add(new StringOption("fake"){
			@Override
			public void setValue(String value) {
				mFake = value;
			}
    	}.setDefaultValue(mFake).setDescription("Do a fake request. Format should be T:I where T are the total seconds and I are the update seconds."));
    	options.add(new BooleanOption("debug") {
			@Override
			public void setValue(boolean value) {
				mDebug = value;
			}
		}.setDefaultValue(mDebug).setDescription("Print the outgoing request instead of sending it."));    	    	
    	return options;
    }
	
	protected Dataset readDataset() throws IOException {
		try {
			if(mDatasetFormat == Format.Csv) {
				return new CsvDatasetReader().read(mDatasetReader);
			} else if(mDatasetFormat == Format.Xml) {
				return new XmlDatasetReader().read(mDatasetReader);
			} else {
				throw new InvalidParameterException("Unknown dataset format");
			}
		} catch(Exception e) {
			throw new IOException(e);
		}
	}
	
	protected DatasetAging readAging() throws IOException {
		int i = mAgingPath.lastIndexOf("/");
		String format;
		File folder;		
		if(i<0) {
			format = mAgingPath;
			folder = new File(".");
		} else {
			format = mAgingPath.substring(i+1);
			folder = new File(mAgingPath.substring(0, i));
		}
		if(!folder.isDirectory()) {
			throw new IllegalArgumentException("Invalid aging directory");
		}
		if(!folder.canRead()) {
			throw new IllegalArgumentException("Cannot read aging directory");
		}
		String parts[] = mAgingPositions.split(",");
		Map<String, Float> positions = new HashMap<String, Float>();
		for(String part : parts) {
			String partParts[] = part.trim().split(":");
			if(partParts.length != 2) {
				throw new IllegalArgumentException("Strange aging position format");
			}
			positions.put(partParts[0], Float.valueOf(partParts[1]));
		}
		AgingFolderReader agingReader = new AgingFolderReader(format, positions);
		return agingReader.read(folder);
	}

	@Override
    protected void setup() throws IOException {
		super.setup();
		String id = "java.BuildModelsRequestClient";
		BuildModelsRequest request;
		if(mFake != null) {
			request = new BuildModelsFakeRequest(id, mFake);
		} else if(mDatasetReader != null) {
			request = new BuildModelsRequest(id, readDataset(), readAging());
		} else {
			throw new InvalidOptionException("No dataset specified");
		}
		mClient = new BuildModelsRequestClient(request,
				mRequestQueue, mRequestExchange, mResponseQueue, mResponseOutput);
		if(mDebug) {
			mClient.setDebug(true);
		} else {		
			mClient.setup(mConnection.createChannel());
		}
	}
	
	@Override
	protected void start() throws IOException
	{
		super.start();
		runClient(mClient);
	}
	
}

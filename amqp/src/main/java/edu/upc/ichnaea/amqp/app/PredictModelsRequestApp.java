package edu.upc.ichnaea.amqp.app;

import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.security.InvalidParameterException;

import edu.upc.ichnaea.amqp.IOUtils;
import edu.upc.ichnaea.amqp.cli.BooleanOption;
import edu.upc.ichnaea.amqp.cli.EnumOption;
import edu.upc.ichnaea.amqp.cli.InvalidOptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.ReadFileOption;
import edu.upc.ichnaea.amqp.cli.StringOption;
import edu.upc.ichnaea.amqp.cli.WriteFileOption;
import edu.upc.ichnaea.amqp.client.PredictModelsRequestClient;
import edu.upc.ichnaea.amqp.data.CsvDatasetReader;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.PredictModelsRequest;
import edu.upc.ichnaea.amqp.xml.XmlDatasetReader;

public class PredictModelsRequestApp extends App {

    enum Format {
        Csv, Xml
    }

    PredictModelsRequestClient mClient;
    Format mDatasetFormat = Format.Csv;
    Reader mDatasetReader;
    FileInputStream mDataStream;
    FileOutputStream mResponseOutput;

    String mResponseQueue = "ichnaea.predict-models.response";
    String mRequestQueue = "ichnaea.predict-models.request";
    String mRequestExchange = "ichnaea.predict-models.request";
    boolean mDebug = false;

    public static void main(String[] args) {
        main(args, new PredictModelsRequestApp());
    }

    @Override
    protected Options getOptions() {
        Options options = super.getOptions();
        options.add(new ReadFileOption("data") {
            @Override
            public void setValue(FileInputStream value) {
                mDataStream = value;
            }
        }.setDescription("The file with the data returned by the predict models request."));
        options.add(new ReadFileOption("dataset") {
            @Override
            public void setValue(FileInputStream value) {
                mDatasetReader = new InputStreamReader(value);
            }
        }.setDescription("The file with the dataset to predict."));
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
        options.add(new StringOption("request-queue") {
            @Override
            public void setValue(String value) {
                mRequestQueue = value;
            }
        }.setDefaultValue(mRequestQueue).setDescription(
                "The queue to send the request."));
        options.add(new StringOption("request-exchange") {
            @Override
            public void setValue(String value) {
                mRequestExchange = value;
            }
        }.setDefaultValue(mRequestExchange).setDescription(
                "The exchange to send the request."));
        options.add(new StringOption("response-queue") {
            @Override
            public void setValue(String value) {
                mResponseQueue = value;
            }
        }.setDefaultValue(mResponseQueue).setDescription(
                "The queue to listen for responses."));
        options.add(new BooleanOption("debug") {
            @Override
            public void setValue(boolean value) {
                mDebug = value;
            }
        }.setDefaultValue(mDebug).setDescription(
                "Print the outgoing request instead of sending it."));
        return options;
    }

    protected Dataset readDataset() throws IOException {
        try {
            if (mDatasetFormat == Format.Csv) {
                return new CsvDatasetReader().read(mDatasetReader);
            } else if (mDatasetFormat == Format.Xml) {
                return new XmlDatasetReader().read(mDatasetReader);
            } else {
                throw new InvalidParameterException("Unknown dataset format");
            }
        } catch (Exception e) {
            throw new IOException(e);
        }
    }
    
    protected byte[] readData() throws IOException {
        return IOUtils.read(mDataStream);
    }

    @Override
    protected void setup() throws IOException {
        super.setup();
        String id = "java.BuildModelsRequestClient";
        if (mDatasetReader == null) {
            throw new InvalidOptionException("No dataset specified");
        }
        if (mDataStream == null) {
            throw new InvalidOptionException("No data specified");
        }
        PredictModelsRequest request = new PredictModelsRequest(id, readDataset(), readData());
        mClient = new PredictModelsRequestClient(request, mRequestQueue,
                mRequestExchange, mResponseQueue, mResponseOutput);
        if (mDebug) {
            mClient.setDebug(true);
        } else {
            mClient.setup(mConnection.createChannel());
        }
    }

    @Override
    protected void start() throws IOException {
        super.start();
        runClient(mClient);
    }

}

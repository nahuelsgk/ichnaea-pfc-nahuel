package edu.upc.ichnaea.amqp.app;

import java.io.IOException;

import edu.upc.ichnaea.amqp.cli.BooleanOption;
import edu.upc.ichnaea.amqp.cli.IntegerOption;
import edu.upc.ichnaea.amqp.cli.InvalidOptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;
import edu.upc.ichnaea.amqp.client.FakeRequestClient;
import edu.upc.ichnaea.amqp.model.FakeRequest;

public class FakeRequestApp extends App {

    FakeRequestClient mClient;

    String mResponseQueue = "ichnaea.fake.response";
    String mRequestQueue = "ichnaea.fake.request";
    String mRequestExchange = "ichnaea.fake.request";
    int mDuration = 10;
    int mInterval = 1;
    boolean mDebug = false;

    public static void main(String[] args) {
        main(args, new FakeRequestApp());
    }

    @Override
    protected Options getOptions() {
        Options options = super.getOptions();
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
        options.add(new IntegerOption("duration") {
            @Override
            public void setValue(int value) throws InvalidOptionException {
                mDuration = value;
            }
        }.setDefaultValue(mDuration).setDescription(
                "The total duration of the fake request."));
        options.add(new IntegerOption("interval") {
            @Override
            public void setValue(int value) throws InvalidOptionException {
                mInterval = value;
            }
        }.setDefaultValue(mInterval).setDescription(
                "The interval in which to send updates."));        
        options.add(new BooleanOption("debug") {
            @Override
            public void setValue(boolean value) {
                mDebug = value;
            }
        }.setDefaultValue(mDebug).setDescription(
                "Print the outgoing request instead of sending it."));
        return options;
    }

    @Override
    protected void setup() throws IOException {
        super.setup();
        String id = "java.FakeRequestApp."+getRandom();
        FakeRequest request = new FakeRequest(id, mDuration, mInterval);
        
        mClient = new FakeRequestClient(request, mRequestQueue,
                mRequestExchange, mResponseQueue);
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

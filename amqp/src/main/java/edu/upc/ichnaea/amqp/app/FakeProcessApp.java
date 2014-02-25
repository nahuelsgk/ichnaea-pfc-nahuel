package edu.upc.ichnaea.amqp.app;

import java.io.IOException;
import java.net.MalformedURLException;

import edu.upc.ichnaea.amqp.cli.IntegerOption;
import edu.upc.ichnaea.amqp.cli.InvalidOptionException;
import edu.upc.ichnaea.amqp.cli.Options;
import edu.upc.ichnaea.amqp.cli.StringOption;
import edu.upc.ichnaea.amqp.cli.BooleanOption;
import edu.upc.ichnaea.amqp.client.FakeProcessClient;
import edu.upc.ichnaea.shell.ShellFactory;
import edu.upc.ichnaea.shell.ShellInterface;

public class FakeProcessApp extends App {

    FakeProcessClient mClient;
    String mShell;
    int mFork = 1;
    String mScriptPath = "./ichnaea.sh";
    String mRequestQueue = "ichnaea.fake.request";
    String mResponseQueues = "ichnaea.fake.response";
    String mResponseExchange = "ichnaea.fake.response";
    boolean mVerbose = false;

    public static void main(String[] args) {
        main(args, new FakeProcessApp());
    }

    protected Options getOptions() {
        Options options = super.getOptions();
        options.add(new StringOption("shell") {
            @Override
            public void setValue(String value) throws InvalidOptionException {
                mShell = value;
            }
        }.setDescription("The url to the remote shell."));
        options.add(new IntegerOption("fork") {
            @Override
            public void setValue(int value) throws InvalidOptionException {
                mFork = value;
            }
        }.setDefaultValue(mFork).setDescription(
                "The max amount of processes to spawn."));
        options.add(new StringOption("ichnaea-script") {
            @Override
            public void setValue(String value) throws InvalidOptionException {
                mScriptPath = value;
            }
        }.setDefaultValue(mScriptPath).setDescription(
                "The path to the ichnaea script."));
        options.add(new StringOption("request-queue") {
            @Override
            public void setValue(String value) {
                mRequestQueue = value;
            }
        }.setDefaultValue(mRequestQueue).setDescription(
                "The queue to listen for requests."));
        options.add(new StringOption("response-queue") {
            @Override
            public void setValue(String value) {
                mResponseQueues = value;
            }
        }.setDefaultValue(mResponseQueues).setDescription(
                "A comma-separated list of queues to send the responses."));
        options.add(new StringOption("response-exchange") {
            @Override
            public void setValue(String value) {
                mResponseExchange = value;
            }
        }.setDefaultValue(mResponseExchange).setDescription(
                "The exchange to send responses."));
        options.add(new BooleanOption("verbose") {
            @Override
            public void setValue(boolean value) {
                mVerbose = value;
            }
        }.setDefaultValue(mVerbose).setDescription(
                "Print the command output."));
        return options;
    }

    @Override
    protected void setup() throws IOException {
        super.setup();
        ShellInterface shell = null;
        try {
            shell = new ShellFactory().create(mShell);
        } catch (MalformedURLException e) {
            throw new InvalidOptionException(e.getMessage());
        }
        mClient = new FakeProcessClient(shell, mScriptPath,
                mRequestQueue, mResponseQueues.split(","), mResponseExchange,
                mFork);
        mClient.setVerbose(mVerbose);
        mClient.setup(mConnection.createChannel());
    }

    @Override
    protected void start() throws IOException {
        super.start();
        runClient(mClient);
    }

}

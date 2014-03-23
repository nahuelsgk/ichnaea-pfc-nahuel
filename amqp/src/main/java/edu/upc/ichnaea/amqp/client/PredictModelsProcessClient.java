package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.util.Calendar;

import javax.xml.parsers.ParserConfigurationException;

import com.rabbitmq.client.AMQP;

import edu.upc.ichnaea.amqp.FileUtils;
import edu.upc.ichnaea.amqp.IOUtils;
import edu.upc.ichnaea.amqp.data.CsvDatasetWriter;
import edu.upc.ichnaea.amqp.model.PredictModelsRequest;
import edu.upc.ichnaea.amqp.model.PredictModelsResponse;
import edu.upc.ichnaea.amqp.model.PredictModelsResult;
import edu.upc.ichnaea.amqp.xml.XmlPredictModelsRequestReader;
import edu.upc.ichnaea.amqp.xml.XmlPredictModelsResponseWriter;
import edu.upc.ichnaea.shell.CommandResultInterface;
import edu.upc.ichnaea.shell.PredictModelsCommand;
import edu.upc.ichnaea.shell.ShellInterface;
import edu.upc.ichnaea.shell.PredictModelsCommandReader;

public class PredictModelsProcessClient extends AbstractProcessClient {

    public PredictModelsProcessClient(ShellInterface shell, String scriptPath,
            String requestQueue, String[] responseQueues,
            String responseExchange, int maxThreads) {
        super(shell, scriptPath, requestQueue, responseQueues, responseExchange,
                maxThreads);
    }

    protected void sendResponse(PredictModelsResponse response, String replyTo)
            throws IOException, ParserConfigurationException {
        if (replyTo == null || replyTo.isEmpty()) {
            return;
        }
        AMQP.BasicProperties properties = new AMQP.BasicProperties().builder()
                .contentType("multipart/mixed").build();
        getLogger().info("writing response xml");
        String responseXml = new XmlPredictModelsResponseWriter().build(response)
                .toString();
        if(mVerbose) {
            getLogger().info(responseXml);
        }
        getChannel().basicPublish(mResponseExchange, replyTo, properties,
                responseXml.getBytes());
    }

    protected void sendRequestUpdates(CommandResultInterface result,
            final Calendar start, final String replyTo) throws IOException {
        new PredictModelsCommandReader(result, mVerbose) {
            @Override
            protected void onUpdate(float percent, Calendar end, PredictModelsResult result){
                getLogger().info("sending status update");
                try {
                    sendRequestUpdate(replyTo, start, end, percent, result);
                } catch (IOException e) {
                    getLogger().warning(
                            "error sending status update: "
                                    + e.getLocalizedMessage());
                }
            }
        }.read();
    }

    protected void sendRequestUpdate(String replyTo, Calendar start, Calendar end,
            float percent, PredictModelsResult result) throws IOException {
        try {
            sendResponse(new PredictModelsResponse(replyTo, start, end,
                    percent, result), replyTo);
        } catch (ParserConfigurationException e) {
            throw new IOException(e);
        }
    }

    protected void processRequest(PredictModelsRequest req,
            final String replyTo) throws IOException, InterruptedException,
            ParserConfigurationException {

        Calendar start = Calendar.getInstance();

        String err = null;
        PredictModelsResponse resp = null;

        if (req.getDataset() == null) {
            err = "No dataset received.";
        }
        if (err != null) {
            resp = new PredictModelsResponse(replyTo, start, null, err);
            getLogger().warning(err);
            sendResponse(resp, replyTo);
            return;
        }

        String datasetPath = FileUtils.tempPath(mShell.getTempPath());
        getLogger().info("writing dataset to " + datasetPath);
        OutputStream out = mShell.writeFile(datasetPath);
        new CsvDatasetWriter(new OutputStreamWriter(out)).write(
                req.getDataset()).close();
        
        String modelsPath = FileUtils.tempPath(mShell.getTempPath());
        getLogger().info("writing models data to " + modelsPath);
        out = mShell.writeFile(modelsPath);
        IOUtils.write(out, req.getData());

        getLogger().info("calling predict models command");
        PredictModelsCommand cmd = new PredictModelsCommand(datasetPath, modelsPath, mVerbose);

        cmd.setScriptPath(mScriptPath);
        getLogger().info(cmd.toString());
        CommandResultInterface result = mShell.run(cmd);

        getLogger().info("reading command result");
        try {
            sendRequestUpdates(result, start, replyTo);
        } catch (IOException e) {
            err = "failed command result: " + e.getMessage();
            getLogger().warning(err);
            Calendar end = Calendar.getInstance();
            resp = new PredictModelsResponse(replyTo, start, end, err);
        }

        getLogger().info("deleting temporary dataset file");
        mShell.removePath(datasetPath);
        
        getLogger().info("deleting temporary models data file");
        mShell.removePath(modelsPath);

        if (resp == null) {
            Calendar end = Calendar.getInstance();
            resp = new PredictModelsResponse(replyTo, start, end);
        }
        getLogger().info("sending result");
        sendResponse(resp, replyTo);
    }
    
    protected void sendRequestUpdate(String replyTo, Calendar start, Calendar end,
            float percent) throws IOException {
        
        try {
            sendResponse(new PredictModelsResponse(replyTo, start, end,
                    percent), replyTo);
        } catch (ParserConfigurationException e) {
            throw new IOException(e);
        }
    }

    protected void processRequest(String replyTo, byte[] body)
            throws Exception {
        getLogger().info("received a request");
        getLogger().info(new String(body));

        getLogger().info("parsing the request");
        PredictModelsRequest req = new XmlPredictModelsRequestReader()
                .read(new String(body));

        getLogger().info("opening shell");
        mShell.open();

        processRequest(req, replyTo);

        getLogger().info("closing shell");
        mShell.close();
    }

    @Override
    public void run() throws IOException {
        super.run();
        getLogger().info(
                "waiting for predict models requests on queue \"" + mRequestQueue
                        + "\"...");
    }

}

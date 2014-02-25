package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.util.Calendar;

import javax.mail.MessagingException;
import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;

import edu.upc.ichnaea.amqp.FileUtils;
import edu.upc.ichnaea.amqp.IOUtils;
import edu.upc.ichnaea.amqp.data.CsvDatasetWriter;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.PredictModelsRequest;
import edu.upc.ichnaea.amqp.model.PredictModelsResponse;
import edu.upc.ichnaea.amqp.xml.XmlPredictModelsRequestReader;
import edu.upc.ichnaea.amqp.xml.XmlPredictModelsResponseWriter;
import edu.upc.ichnaea.shell.CommandResultInterface;
import edu.upc.ichnaea.shell.PredictModelsCommand;
import edu.upc.ichnaea.shell.ShellInterface;

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
        String responseXml = new XmlPredictModelsResponseWriter().build(response)
                .toString();
        getChannel().basicPublish(mResponseExchange, replyTo, properties,
                responseXml.getBytes());
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
        
        String dataPath = FileUtils.tempPath(mShell.getTempPath());
        getLogger().info("writing data to " + dataPath);
        out = mShell.writeFile(dataPath);
        new OutputStreamWriter(out).write(new String(req.getData()));

        getLogger().info("calling predict models command");
        PredictModelsCommand cmd = new PredictModelsCommand(datasetPath, mVerbose);

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
        
        if (resp == null) {
            getLogger().info("reading output file in " + cmd.getOutputPath());
            Calendar end = Calendar.getInstance();

            try {
                byte[] data = IOUtils
                        .read(mShell.readFile(cmd.getOutputPath()));
                resp = new PredictModelsResponse(replyTo, start, end);
            } catch (IOException e) {
                err = "failed to read output file: " + e.getMessage();
                getLogger().warning(err);
                resp = new PredictModelsResponse(replyTo, start, end, err);
            }
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

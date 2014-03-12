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
import edu.upc.ichnaea.amqp.data.AgingFolderWriter;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsRequestReader;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsResponseWriter;
import edu.upc.ichnaea.shell.BuildModelsCommand;
import edu.upc.ichnaea.shell.CommandResultInterface;
import edu.upc.ichnaea.shell.ShellInterface;

public class BuildModelsProcessClient extends AbstractProcessClient {

    public BuildModelsProcessClient(ShellInterface shell, String scriptPath,
            String requestQueue, String[] responseQueues,
            String responseExchange, int maxThreads) {
        super(shell, scriptPath, requestQueue, responseQueues, responseExchange,
                maxThreads);
    }

    protected void sendResponse(BuildModelsResponse response, String replyTo)
            throws IOException, ParserConfigurationException {
        if (replyTo == null || replyTo.isEmpty()) {
            return;
        }
        AMQP.BasicProperties properties = new AMQP.BasicProperties().builder()
                .contentType("multipart/mixed").build();
        String responseXml = new XmlBuildModelsResponseWriter().build(response)
                .toString();
        getChannel().basicPublish(mResponseExchange, replyTo, properties,
                responseXml.getBytes());
    }

    protected void sendRequestUpdate(String replyTo, Calendar start, Calendar end,
            float percent) throws IOException {
        
        try {
            sendResponse(new BuildModelsResponse(replyTo, start, end,
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
        BuildModelsRequest req = new XmlBuildModelsRequestReader()
                .read(new String(body));

        getLogger().info("opening shell");
        mShell.open();

        Calendar start = Calendar.getInstance();

        String err = null;
        BuildModelsResponse resp = null;

        if (req.getDataset() == null) {
            err = "No dataset received.";
        } else if (req.getAging() == null) {
            err = "No aging received.";
        }
        if (err != null) {
            resp = new BuildModelsResponse(replyTo, start, null, err);
            getLogger().warning(err);
            sendResponse(resp, replyTo);
            return;
        }

        String datasetPath = FileUtils.tempPath(mShell.getTempPath());
        getLogger().info("writing dataset to " + datasetPath);
        OutputStream out = mShell.writeFile(datasetPath);
        new CsvDatasetWriter(new OutputStreamWriter(out)).write(
                req.getDataset()).close();

        String agingPath = FileUtils.tempPath(mShell.getTempPath());
        getLogger().info("writing aging to " + agingPath);
        mShell.createFolder(agingPath);
        String agingFormat = agingPath + "/env%column%-%aging%.txt";
        new AgingFolderWriter(agingFormat) {
            @Override
            protected OutputStream createFile(String path) throws IOException {
                return mShell.writeFile(path);
            }
        }.write(req.getAging());

        getLogger().info("calling build models command");
        BuildModelsCommand cmd = new BuildModelsCommand(datasetPath, agingPath, mVerbose);

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
            resp = new BuildModelsResponse(replyTo, start, end, err);
        }

        /*
        getLogger().info("deleting temporary dataset file");
        mShell.removePath(datasetPath);

        getLogger().info("deleting temporary aging folder");
        mShell.removePath(agingPath);
        */

        if (resp == null) {
            getLogger().info("reading output file in " + cmd.getModelsPath());
            Calendar end = Calendar.getInstance();

            try {
                byte[] data = IOUtils
                        .read(mShell.readFile(cmd.getModelsPath()));
                resp = new BuildModelsResponse(replyTo, start, end, data);
            } catch (IOException e) {
                err = "failed to read output file: " + e.getMessage();
                getLogger().warning(err);
                resp = new BuildModelsResponse(replyTo, start, end, err);
            }
        }
        getLogger().info("sending result");
        sendResponse(resp, replyTo);

        getLogger().info("closing shell");
        mShell.close();
    }

   

    @Override
    public void run() throws IOException {
        super.run();
        getLogger().info(
                "waiting for build models requests on queue \"" + mRequestQueue
                        + "\"...");
    }

}

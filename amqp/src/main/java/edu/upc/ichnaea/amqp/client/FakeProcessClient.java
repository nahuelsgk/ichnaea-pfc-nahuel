package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import java.util.Calendar;
import javax.xml.parsers.ParserConfigurationException;

import com.rabbitmq.client.AMQP;

import edu.upc.ichnaea.amqp.model.FakeRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;
import edu.upc.ichnaea.amqp.xml.XmlFakeRequestReader;
import edu.upc.ichnaea.amqp.xml.XmlFakeResponseWriter;
import edu.upc.ichnaea.shell.CommandResultInterface;
import edu.upc.ichnaea.shell.FakeCommand;
import edu.upc.ichnaea.shell.ShellInterface;
import edu.upc.ichnaea.shell.UpdateProgressCommandReader;

public class FakeProcessClient extends AbstractProcessClient {

    public FakeProcessClient(ShellInterface shell, String scriptPath,
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
                .contentType("text/xml").build();
        String responseXml = new XmlFakeResponseWriter().build(response)
                .toString();
        getChannel().basicPublish(mResponseExchange, replyTo, properties,
                responseXml.getBytes());
    }

    protected void sendRequestUpdates(CommandResultInterface result,
            final Calendar start, final String replyTo) throws IOException {
        new UpdateProgressCommandReader(result, mVerbose) {
            @Override
            protected void onUpdate(float percent, Calendar end) {
                getLogger().info("sending status update");
                try {
                    sendRequestUpdate(replyTo, start, end, percent);
                } catch (IOException e) {
                    getLogger().warning(
                            "error sending status update: "
                                    + e.getLocalizedMessage());
                }
            }
        }.read();
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
        FakeRequest req = new XmlFakeRequestReader()
                .read(new String(body));

        getLogger().info("opening shell");
        mShell.open();

        Calendar start = Calendar.getInstance();
        getLogger().info("calling fake command");
        FakeCommand cmd = new FakeCommand(req.getDuration(), req.getInterval());

        cmd.setScriptPath(mScriptPath);
        getLogger().info(cmd.toString());
        CommandResultInterface result = mShell.run(cmd);

        getLogger().info("reading command result");
        try {
            sendRequestUpdates(result, start, replyTo);
        } catch (IOException e) {
            String err = "failed command result: " + e.getMessage();
            getLogger().warning(err);
            Calendar end = Calendar.getInstance();
            BuildModelsResponse resp = new BuildModelsResponse(replyTo, start,
                    end, err);
            getLogger().info("sending result");
            sendResponse(resp, replyTo);
            return;
        }

        getLogger().info("closing shell");
        mShell.close();
    }

    @Override
    public void run() throws IOException {
        super.run();
        getLogger().info(
                "waiting for fake requests on queue \"" + mRequestQueue
                        + "\"...");
    }

}

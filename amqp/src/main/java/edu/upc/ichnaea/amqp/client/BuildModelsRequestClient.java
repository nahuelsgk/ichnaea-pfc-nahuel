package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import java.io.OutputStream;

import javax.mail.MessagingException;
import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsRequestWriter;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsResponseReader;
import edu.upc.ichnaea.amqp.xml.XmlPrettyFormatter;

public class BuildModelsRequestClient extends AbstractRequestClient {
    
    BuildModelsRequest mRequest;
    OutputStream mResponseOutput;

    public BuildModelsRequestClient(BuildModelsRequest request, String requestQueue,
            String requestExchange, String responseQueue, OutputStream output) {
        super(requestQueue, requestExchange, responseQueue);
        mRequest = request;
        mResponseOutput = output;
    }

    @Override
    protected String setupRequest(AMQP.BasicProperties.Builder builder) throws IOException {
        builder.contentType("text/xml");
        try {
            return new XmlBuildModelsRequestWriter().build(mRequest)
            .toString();
        } catch (ParserConfigurationException e) {
            throw new IOException(e);
        }
    }
    
    @Override    
    protected void debugRequest() throws IOException {
        try {
            String xml = new XmlBuildModelsRequestWriter().build(mRequest).toString();
            getLogger().info(new XmlPrettyFormatter().format(xml));
        } catch (ParserConfigurationException e) {
            throw new IOException(e);
        }
    }
    
    @Override
    protected String getRequestId() {
        return mRequest.getId();
    }
    
    @Override    
    protected void sendRequest(String routingKey)
            throws ParserConfigurationException, IOException {
        getLogger().info(
                "sending build models request to exchange \""
                        + mRequestExchange + "\"...");

        super.sendRequest(routingKey);
    }

    @Override    
    protected void processResponse(byte[] body) throws IOException {
        try {
            BuildModelsResponse resp = new XmlBuildModelsResponseReader()
                    .read(new String(body));
            if (mResponseOutput != null && resp.hasData()) {
                getLogger().info("writing build models response to a file ...");
                mResponseOutput.write(resp.getData());
                mResponseOutput.close();
            }
        } catch (SAXException | MessagingException e) {
            throw new IOException(e);
        }
    }

    @Override
    public void run() throws IOException {
        super.run();
        getLogger().info(
                "waiting for build models response on queue \""
                        + mResponseQueue + "\"...");
    }

}

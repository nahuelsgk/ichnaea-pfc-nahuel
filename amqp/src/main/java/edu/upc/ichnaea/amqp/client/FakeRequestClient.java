package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;

import edu.upc.ichnaea.amqp.model.FakeRequest;
import edu.upc.ichnaea.amqp.model.FakeResponse;
import edu.upc.ichnaea.amqp.xml.XmlFakeRequestWriter;
import edu.upc.ichnaea.amqp.xml.XmlFakeResponseReader;
import edu.upc.ichnaea.amqp.xml.XmlPrettyFormatter;

public class FakeRequestClient extends AbstractRequestClient {
    
    FakeRequest mRequest;

    public FakeRequestClient(FakeRequest request, String requestQueue,
            String requestExchange, String responseQueue) {
        super(requestQueue, requestExchange, responseQueue);
        mRequest = request;
    }

    @Override
    protected String setupRequest(AMQP.BasicProperties.Builder builder) throws IOException {
        builder.contentType("text/xml");
        try {
            return new XmlFakeRequestWriter().build(mRequest)
            .toString();
        } catch (ParserConfigurationException e) {
            throw new IOException(e);
        }
    }
    
    @Override    
    protected void debugRequest() throws IOException {
        try {
            String xml = new XmlFakeRequestWriter().build(mRequest).toString();
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
                "sending fake request to exchange \""
                        + mRequestExchange + "\"...");

        super.sendRequest(routingKey);
    }

    @Override    
    protected void processResponse(byte[] body) throws IOException {
        try {
            FakeResponse resp = new XmlFakeResponseReader()
                    .read(new String(body));
            super.processProgressResponse(resp);
        } catch (SAXException e) {
            throw new IOException(e);
        }
    }

    @Override
    public void run() throws IOException {
        super.run();
        getLogger().info(
                "waiting for fake response on queue \""
                        + mResponseQueue + "\"...");
    }

}

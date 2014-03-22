package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import java.io.OutputStream;

import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;

import edu.upc.ichnaea.amqp.model.PredictModelsRequest;
import edu.upc.ichnaea.amqp.model.PredictModelsResponse;
import edu.upc.ichnaea.amqp.model.PredictModelsResult;
import edu.upc.ichnaea.amqp.xml.XmlPredictModelsRequestWriter;
import edu.upc.ichnaea.amqp.xml.XmlPredictModelsResponseReader;
import edu.upc.ichnaea.amqp.xml.XmlPrettyFormatter;

public class PredictModelsRequestClient extends AbstractRequestClient {

    PredictModelsRequest mRequest;
    OutputStream mResponseOutput;
    
    boolean mDebug = false;

    public PredictModelsRequestClient(PredictModelsRequest request,
            String requestQueue, String requestExchange, String responseQueue,
            OutputStream output) {
        super(requestQueue, requestExchange, responseQueue);
        mRequest = request;
        mResponseOutput = output;
    }
    
    @Override
    protected String setupRequest(AMQP.BasicProperties.Builder builder) throws IOException {
        builder.contentType("text/xml");
        try {
            return new XmlPredictModelsRequestWriter().build(mRequest)
            .toString();
        } catch (ParserConfigurationException e) {
            throw new IOException(e);
        }
    }
    
    @Override
    protected void debugRequest() throws IOException {
        try {
            String xml = new XmlPredictModelsRequestWriter().build(mRequest).toString();
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
                "sending predict models request to exchange \""
                        + mRequestExchange + "\"...");
        super.sendRequest(routingKey);
    }    

    @Override
    protected void processResponse(byte[] body) throws IOException {
        try {
            PredictModelsResponse resp = new XmlPredictModelsResponseReader()
                    .read(new String(body));
            processProgressResponse(resp);
            PredictModelsResult result = resp.getResult();                    
            if(!result.isEmpty()) {
                getLogger().info("result: samples " + result.getPredictedSamples()
                    + "/" + result.getTotalSamples());
            }
            if(result.isFinished()) {
                getLogger().info("result: finished with " + 100*result.getTestError()+"% error");
                String str = result.toString();
                str += "\n----\n";
                if(mResponseOutput != null) {
                    mResponseOutput.write(str.getBytes());
                } else {
                    getLogger().info(str);
                }
            }
        } catch (SAXException e) {
            throw new IOException(e);
        }
    }

    @Override
    public void run() throws IOException {
        super.run();
        getLogger().info(
                "waiting for predict models response on queue \""
                        + mResponseQueue + "\"...");
    }

}

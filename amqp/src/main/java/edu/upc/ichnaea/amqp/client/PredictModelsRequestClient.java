package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import java.io.OutputStream;
import java.text.SimpleDateFormat;

import javax.mail.MessagingException;
import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;
import com.rabbitmq.client.AMQP.BasicProperties;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

import edu.upc.ichnaea.amqp.model.BuildModelsResponse;
import edu.upc.ichnaea.amqp.model.PredictModelsRequest;
import edu.upc.ichnaea.amqp.model.PredictModelsResponse;
import edu.upc.ichnaea.amqp.xml.XmlPredictModelsRequestWriter;
import edu.upc.ichnaea.amqp.xml.XmlPredictModelsResponseReader;
import edu.upc.ichnaea.amqp.xml.XmlPrettyFormatter;

public class PredictModelsRequestClient extends Client {

    PredictModelsRequest mRequest;
    OutputStream mResponseOutput;
    String mRequestExchange;
    String mRequestQueue;
    String mResponseQueue;
    boolean mDebug = false;

    public PredictModelsRequestClient(PredictModelsRequest request,
            String requestQueue, String requestExchange, String responseQueue,
            OutputStream output) {
        mRequest = request;
        mResponseOutput = output;
        mRequestQueue = requestQueue;
        mRequestExchange = requestExchange;
        mResponseQueue = responseQueue;
    }

    public void setDebug(boolean debug) {
        mDebug = debug;
    }

    protected void sendRequest(String routingKey)
            throws ParserConfigurationException, IOException {
        getLogger().info(
                "sending predict models request to exchange \""
                        + mRequestExchange + "\"...");

        String xml = new XmlPredictModelsRequestWriter().build(mRequest)
                .toString();

        if (mDebug) {
            getLogger().info(new XmlPrettyFormatter().format(xml));
            setFinished(true);
        } else {
            Channel ch = getChannel();
            BasicProperties props = new AMQP.BasicProperties.Builder()
                    .contentType("text/xml").replyTo(routingKey).build();

            ch.basicPublish(mRequestExchange, "", props, xml.getBytes());
        }
    }

    protected void processResponse(byte[] body) throws IOException,
            SAXException, MessagingException {
        PredictModelsResponse resp = new XmlPredictModelsResponseReader()
                .read(new String(body));

        if (resp.hasError()) {
            getLogger().warning("got error: " + resp.getError());
            setFinished(true);
            return;
        }

        float progress = resp.getProgress();
        SimpleDateFormat f = new SimpleDateFormat("EEE MMM dd HH:mm:ss z yyyy");

        if (progress < 1) {
            getLogger().info("request update");
            int percent = Math.round(progress * 100);
            getLogger().info("progress: " + percent + "%");
            if (resp.hasEnd()) {
                getLogger().info(
                        "estimated end time: "
                                + f.format(resp.getEnd().getTime()));
            }
        } else {
            getLogger().info("request finished");
            if (resp.hasStart()) {
                getLogger().info(
                        "start time: " + f.format(resp.getStart().getTime()));
            }
            if (resp.hasEnd()) {
                getLogger().info(
                        "end time: " + f.format(resp.getEnd().getTime()));
            }

            setFinished(true);
        }
    }

    @Override
    public void setup(Channel channel) throws IOException {
        super.setup(channel);
        channel.queueDeclare(mRequestQueue, false, false, false, null);
        channel.exchangeDeclare(mRequestExchange, "direct", true);
        channel.queueBind(mRequestQueue, mRequestExchange, "");
        channel.queueDeclare(mResponseQueue, false, false, false, null);
    }

    @Override
    public void run() throws IOException {
        Channel ch = getChannel();
        String routingKey = mRequest.getId();
        boolean autoAck = true;

        if (ch != null) {
            ch.basicConsume(mResponseQueue, autoAck, new DefaultConsumer(ch) {
                @Override
                public void handleDelivery(String consumerTag,
                        Envelope envelope, AMQP.BasicProperties properties,
                        byte[] body) throws IOException {
                    try {
                        processResponse(body);
                    } catch (Exception e) {
                        throw new IOException(e);
                    }
                }
            });
        }
        try {
            sendRequest(routingKey);
        } catch (Exception e) {
            throw new IOException(e);
        }

        getLogger().info(
                "waiting for predict models response on queue \""
                        + mResponseQueue + "\"...");
    }

}

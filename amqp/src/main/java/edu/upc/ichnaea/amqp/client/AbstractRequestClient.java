package edu.upc.ichnaea.amqp.client;

import java.io.IOException;
import java.text.SimpleDateFormat;

import javax.xml.parsers.ParserConfigurationException;

import com.rabbitmq.client.AMQP;
import com.rabbitmq.client.AMQP.BasicProperties;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

import edu.upc.ichnaea.amqp.model.ProgressResponse;
public abstract class AbstractRequestClient extends Client {

    String mRequestExchange;
    String mRequestQueue;
    String mResponseQueue;
    boolean mDebug = false;

    public AbstractRequestClient(String requestQueue, String requestExchange, String responseQueue) {
        mRequestQueue = requestQueue;
        mRequestExchange = requestExchange;
        mResponseQueue = responseQueue;
    }

    public void setDebug(boolean debug) {
        mDebug = debug;
    }
    
    abstract protected String setupRequest(AMQP.BasicProperties.Builder builder) throws IOException;
    abstract protected String getRequestId();
    abstract protected void debugRequest() throws IOException;
    abstract protected void processResponse(byte[] resp) throws IOException;

    protected void sendRequest(String routingKey)
            throws ParserConfigurationException, IOException {

        if (mDebug) {
            debugRequest();
            setFinished(true);
        } else {
            
            new AMQP.BasicProperties.Builder();
            
            Channel ch = getChannel();
            AMQP.BasicProperties.Builder builder = new AMQP.BasicProperties.Builder();
            String data = setupRequest(builder);
            BasicProperties props = builder.replyTo(routingKey).build();
            ch.basicPublish(mRequestExchange, "", props, data.getBytes());
        }
    }
    
    protected void processResponse(ProgressResponse resp) {
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
        String routingKey = getRequestId();
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
    }

}

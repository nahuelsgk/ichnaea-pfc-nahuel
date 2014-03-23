package edu.upc.ichnaea.amqp.client;

import java.io.IOException;

import com.rabbitmq.client.AMQP;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

import edu.upc.ichnaea.shell.ShellInterface;

public class AbstractProcessClient extends Client {

    ShellInterface mShell;
    int mMaxThreads;
    int mCurrentThreads = 0;
    String mScriptPath;
    String mRequestQueue;
    String[] mResponseQueues;
    String mResponseExchange;
    boolean mVerbose = false;

    public AbstractProcessClient(ShellInterface shell, String scriptPath,
            String requestQueue, String[] responseQueues,
            String responseExchange, int maxThreads) {
        mShell = shell;
        mMaxThreads = maxThreads;
        mScriptPath = scriptPath;
        mRequestQueue = requestQueue;
        mResponseQueues = responseQueues;
        mResponseExchange = responseExchange;
    }

    public void setVerbose(boolean verbose) {
        mVerbose = verbose;
    }
    
    protected void processRequest(String replyTo, byte[] body)
            throws Exception {
        getLogger().info("received a request");
        getLogger().info(new String(body));
    }

    synchronized protected void updateCurrentThreads(int change) {
        mCurrentThreads += change;
    }

    synchronized boolean maxThreadsReached() {
        return mCurrentThreads >= mMaxThreads;
    }

    protected void spawnProcessRequest(final String replyTo, final byte[] body) {
        updateCurrentThreads(1);

        Thread task = new Thread() {
            public void run() {
                try {
                    processRequest(replyTo, body);
                } catch (Exception e) {
                    getLogger().warning(
                            "error processing request: "
                                    + e.getLocalizedMessage());
                }
                synchronized (BuildModelsProcessClient.class) {
                    updateCurrentThreads(-1);
                }
            }
        };

        if (maxThreadsReached()) {
            getLogger().info(
                    "running last task of " + mMaxThreads + " in main thread");
            task.run();
        } else {
            getLogger().info(
                    "spawning task thread " + mCurrentThreads + " of "
                            + mMaxThreads);
            task.start();
        }
    }

    @Override
    public void setup(Channel channel) throws IOException {
        super.setup(channel);
        channel.exchangeDeclare(mResponseExchange, "fanout", true);
        for (String responseQueue : mResponseQueues) {
            channel.queueDeclare(responseQueue, false, false, false, null);
            channel.queueBind(responseQueue, mResponseExchange, "");
        }
        channel.queueDeclare(mRequestQueue, false, false, false, null);
    }

    @Override
    public void run() throws IOException {
        boolean autoAck = true;
        final Channel ch = getChannel();

        ch.basicConsume(mRequestQueue, autoAck, new DefaultConsumer(ch) {
            @Override
            public void handleDelivery(String consumerTag, Envelope envelope,
                    final AMQP.BasicProperties properties, final byte[] body) {

                spawnProcessRequest(properties.getReplyTo(), body);
            }
        });
    }

}

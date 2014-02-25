package edu.upc.ichnaea.amqp.client;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

import edu.upc.ichnaea.amqp.IOUtils;
import edu.upc.ichnaea.shell.CommandResultInterface;
import edu.upc.ichnaea.shell.ShellInterface;

public class AbstractProcessClient extends Client {

    ShellInterface mShell;
    int mMaxThreads;
    int mCurrentThreads = 0;
    String mScriptPath;
    String mTimeFormat = "EEE MMM dd HH:mm:ss z yyyy";
    String mRegexPercentage = "(\\d*)%";
    String mRegexEndTime = "^ *finish: *(.*) *$";
    String mRequestQueue;
    String[] mResponseQueues;
    String mResponseExchange;
    boolean mVerbose = false;

    public abstract class CommandReader {

        public CommandReader(CommandResultInterface result) throws IOException {
            BufferedReader in = new BufferedReader(new InputStreamReader(
                    result.getInputStream()));
            Pattern regexPercent = Pattern.compile(mRegexPercentage);
            Pattern regexEndTime = Pattern.compile(mRegexEndTime);
            SimpleDateFormat dateFormat = new SimpleDateFormat(mTimeFormat);
            float percent = 0;
            Calendar end = null;
            String line = null;
            String error = null;
            boolean updated = false;

            while (!result.finished()) {
                line = in.readLine();
                if (line == null) {
                    break;
                }
                if(mVerbose) {
                    getLogger().info(line);
                }
                updated = false;
                Matcher m = regexPercent.matcher(line);
                if (m.find()) {
                    try {
                        percent = Float.parseFloat(m.group(1));
                    } catch (NumberFormatException e) {
                        percent = 0;
                    }
                    if (percent > 1) {
                        percent /= 100;
                    }
                    updated = true;
                }
                m = regexEndTime.matcher(line);
                if (m.find()) {
                    end = Calendar.getInstance();
                    try {
                        end.setTime(dateFormat.parse(m.group(1)));
                    } catch (ParseException e) {
                        end = null;
                    }
                    updated = true;
                }
                if (updated) {
                    onUpdate(percent, end);
                }
            }

            result.close();

            int exitStatus = result.getExitStatus();
            if (exitStatus != 0) {
                InputStream es = result.getErrorStream();
                if (es.available() > 0) {
                    error = new String(IOUtils.read(es));
                } else {
                    error = "Got command exit status " + exitStatus;
                }
            }

            if (error != null) {
                throw new IOException(error);
            }
        }

        protected abstract void onUpdate(float percent, Calendar end);
    };

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
    
    protected void sendRequestUpdate(String id, Calendar start, Calendar end,
            float percent) throws IOException {
    }

    protected void sendRequestUpdates(CommandResultInterface result,
            final Calendar start, final String replyTo) throws IOException {
        new CommandReader(result) {
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
        };
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

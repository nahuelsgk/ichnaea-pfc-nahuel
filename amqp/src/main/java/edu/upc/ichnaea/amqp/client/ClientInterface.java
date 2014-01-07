package edu.upc.ichnaea.amqp.client;

import java.io.IOException;

import com.rabbitmq.client.Channel;

public interface ClientInterface {

    public void setup(Channel channel) throws IOException;

    public void run() throws IOException;

    public boolean hasFinished();

}

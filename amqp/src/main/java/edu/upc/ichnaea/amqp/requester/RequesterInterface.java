package edu.upc.ichnaea.amqp.requester;

import java.io.IOException;

public interface RequesterInterface
{
	public MessageInterface request() throws IOException;
}
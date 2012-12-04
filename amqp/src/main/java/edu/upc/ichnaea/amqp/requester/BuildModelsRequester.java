package edu.upc.ichnaea.amqp.requester;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;

import edu.upc.ichnaea.amqp.model.BuildModelsMessage;
import edu.upc.ichnaea.amqp.xml.BuildModelsMessageDocument;

public class BuildModelsRequester extends Requester {

	protected BuildModelsMessage mMessage;
	
	@Override
	public MessageInterface request() throws IOException {
		try {
			return new StringMessage(new BuildModelsMessageDocument(mMessage).toString());
		} catch (ParserConfigurationException e) {
			throw new IOException(e.getMessage());
		}
	}

}

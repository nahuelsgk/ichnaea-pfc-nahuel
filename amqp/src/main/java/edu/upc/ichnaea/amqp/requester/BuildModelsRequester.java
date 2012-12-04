package edu.upc.ichnaea.amqp.requester;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;

import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.xml.BuildModelsBuilder;

public class BuildModelsRequester extends Requester {

	protected BuildModels mMessage;
	
	@Override
	public MessageInterface request() throws IOException {
		try {
			return new StringMessage(new BuildModelsBuilder().build(mMessage).toString());
		} catch (ParserConfigurationException e) {
			throw new IOException(e.getMessage());
		}
	}

}

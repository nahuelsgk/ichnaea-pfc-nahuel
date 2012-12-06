package edu.upc.ichnaea.amqp.requester;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;

import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsWriter;

public class BuildModelsRequester extends Requester {

	protected BuildModels mData;
	
	public BuildModelsRequester(BuildModels data) {
		mData = data;
	}
	
	@Override
	public MessageInterface request() throws IOException {
		try {
			return new StringMessage(new XmlBuildModelsWriter().build(mData).toString());
		} catch (ParserConfigurationException e) {
			throw new IOException(e.getMessage());
		}
	}

}

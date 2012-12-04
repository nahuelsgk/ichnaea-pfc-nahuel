package edu.upc.ichnaea.amqp.app;

import edu.upc.ichnaea.amqp.requester.BuildModelsRequester;
import edu.upc.ichnaea.amqp.requester.RequesterInterface;

public class BuildModelsRequestApp extends RequestApp {

	@Override
	protected RequesterInterface createRequester() {
		return new BuildModelsRequester();
	}
	
  
}

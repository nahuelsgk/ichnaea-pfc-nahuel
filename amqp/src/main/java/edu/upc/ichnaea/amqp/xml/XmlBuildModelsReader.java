package edu.upc.ichnaea.amqp.xml;

import edu.upc.ichnaea.amqp.model.BuildModels;

public class XmlBuildModelsReader extends XmlReader<BuildModelsHandler> {

	public XmlBuildModelsReader() {
		super(new BuildModelsHandler());
	}
	
	public BuildModels getData()
	{
		return getHandler().getData();
	}

}

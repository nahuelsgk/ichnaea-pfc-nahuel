package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;
import java.io.Reader;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;

public class XmlBuildModelsRequestReader extends XmlReader<BuildModelsRequestHandler> {

	public XmlBuildModelsRequestReader() {
		super(new BuildModelsRequestHandler());
	}
	
	public BuildModelsRequest getData()
	{
		return getHandler().getData();
	}
	
	public BuildModelsRequest read(String xml) throws SAXException, IOException {
		super.parse(xml);
		return getData();
	}	
	
	public BuildModelsRequest read(Reader reader) throws SAXException, IOException {
		super.parse(reader);
		return getData();
	}
	

}

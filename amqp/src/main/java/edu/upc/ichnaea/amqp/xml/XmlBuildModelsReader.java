package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;
import java.io.Reader;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModels;

public class XmlBuildModelsReader extends XmlReader<BuildModelsHandler> {

	public XmlBuildModelsReader() {
		super(new BuildModelsHandler());
	}
	
	public BuildModels getData()
	{
		return getHandler().getData();
	}
	
	public BuildModels read(String xml) throws SAXException, IOException {
		super.parse(xml);
		return getData();
	}	
	
	public BuildModels read(Reader reader) throws SAXException, IOException {
		super.parse(reader);
		return getData();
	}
	

}

package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsFakeRequest;

public class XmlBuildModelsRequestWriter extends XmlWriter {
	
	public XmlBuildModelsRequestWriter() throws ParserConfigurationException {
		super("request");
	}

	public XmlBuildModelsRequestWriter build(BuildModelsRequest data) {
		Element root = getRoot();

		root.setAttribute("id", String.valueOf(data.getId()));
		root.setAttribute("type", "build_models");
		if(data instanceof BuildModelsFakeRequest) {
			BuildModelsFakeRequest fakeData = (BuildModelsFakeRequest) data;
			root.setAttribute("fake", fakeData.toString());
		} else {
			Element datasetXml = appendChild("dataset");
			new XmlDatasetWriter(getDocument(), datasetXml).build(data.getDataset());			
		}
		
		return this;
	}

}
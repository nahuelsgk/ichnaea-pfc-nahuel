package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;

public class XmlBuildModelsRequestWriter extends XmlWriter {
	
	public XmlBuildModelsRequestWriter() throws ParserConfigurationException {
		super("request");
	}

	public XmlBuildModelsRequestWriter build(BuildModelsRequest data) {
		Element root = getRoot();

		root.setAttribute("season", data.getSeason().toString().toLowerCase());
		root.setAttribute("type", "build_models");
		
		Element datasetXml = appendChild("dataset");
		new XmlDatasetWriter(getDocument(), datasetXml).build(data.getDataset());
		
		return this;
	}

}

package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.BuildModels;

public class XmlBuildModelsWriter extends XmlWriter {
	
	public XmlBuildModelsWriter() throws ParserConfigurationException {
		super("message");
	}

	public XmlBuildModelsWriter build(BuildModels data) {
		Element root = getRoot();

		root.setAttribute("season", data.getSeason().toString().toLowerCase());
		root.setAttribute("type", "build_models");
		
		Element datasetXml = appendChild("dataset");
		new XmlDatasetWriter(getDocument(), datasetXml).build(data.getDataset());
		
		return this;
	}

}

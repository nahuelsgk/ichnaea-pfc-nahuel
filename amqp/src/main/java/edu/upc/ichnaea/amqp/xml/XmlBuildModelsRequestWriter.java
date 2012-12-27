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

		root.setAttribute("id", String.valueOf(data.getId()));
		root.setAttribute("type", "build_models");
		root.setAttribute("section", String.valueOf(data.getSection()));
		root.setAttribute("season", data.getSeason().toString().toLowerCase());
		
		Element datasetXml = appendChild("dataset");
		new XmlDatasetWriter(getDocument(), datasetXml).build(data.getDataset());
		
		return this;
	}

}
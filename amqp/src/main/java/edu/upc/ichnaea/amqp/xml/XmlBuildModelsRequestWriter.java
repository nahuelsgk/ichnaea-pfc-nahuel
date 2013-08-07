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
		Element xmlRoot = getRoot();

		xmlRoot.setAttribute("id", String.valueOf(data.getId()));
		xmlRoot.setAttribute("type", "build_models");
		if(data instanceof BuildModelsFakeRequest) {
			BuildModelsFakeRequest fakeData = (BuildModelsFakeRequest) data;
			xmlRoot.setAttribute("fake", fakeData.toString());
		} else {
			if(!data.getDataset().isEmpty()) {
				Element xmlDataset = appendChild("dataset");
				new XmlDatasetWriter(getDocument(), xmlDataset).build(data.getDataset());
			}
			if(!data.getAging().isEmpty()) {
				Element xmlAgings = appendChild("agings");
				new XmlDatasetAgingWriter(getDocument(), xmlAgings).build(data.getAging());
			}
		}
		return this;
	}

}
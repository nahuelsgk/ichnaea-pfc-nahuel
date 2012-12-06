package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.model.BuildModels.Season;

public class BuildModelsBuilder extends DocumentBuilder {
	
	public BuildModelsBuilder() throws ParserConfigurationException {
		this(new Document());
	}
	
	public BuildModelsBuilder(Document doc) {
		super(doc, "message");
	}
	
	public Document build(BuildModels data) {
		Element root = getRoot();

		if(data.getSeason() == Season.Summer) {
			root.setAttribute("season", "summer");
		} else if(data.getSeason() == Season.Winter ) {
			root.setAttribute("season", "winter");
		}
		root.setAttribute("type", "build_models");
		
		Element datasetXml = appendChild("dataset");
		new DatasetBuilder(this, datasetXml).build(data.getDataset());
		
		return getDocument();
	}

}

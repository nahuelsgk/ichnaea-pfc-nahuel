package edu.upc.ichnaea.amqp.xml;

import edu.upc.ichnaea.amqp.model.Dataset;

public class XmlDatasetReader extends XmlReader<DatasetHandler> {

	public XmlDatasetReader(DatasetHandler handler) {
		super(new DatasetHandler());
	}
	
	public Dataset getData() {
		return getHandler().getDataset();
	}

}

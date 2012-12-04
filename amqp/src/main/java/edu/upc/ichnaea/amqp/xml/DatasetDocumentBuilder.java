package edu.upc.ichnaea.amqp.xml;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.Dataset;

public class DatasetDocumentBuilder extends GenericDatasetBuilder<Float, Dataset> {

	public DatasetDocumentBuilder(Document doc) {
		super(doc);
	}

	public DatasetDocumentBuilder(DocumentBuilder builder, Element root) {
		super(builder, root);
	}	

}

package edu.upc.ichnaea.amqp.xml;

import edu.upc.ichnaea.amqp.model.Dataset;

public class DatasetHandler extends GenericDatasetHandler<Float, Dataset> {

	@Override
	protected Float stringToValue(String text) {
		return Float.valueOf(text);
	}

	@Override
	protected Dataset createDataset() {
		return new Dataset();
	}

}

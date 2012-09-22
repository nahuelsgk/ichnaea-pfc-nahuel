package edu.upc.ichnaea.amqp.xml;

import edu.upc.ichnaea.amqp.model.ModelsDataset;

public class ModelsDatasetHandler extends GenericDatasetHandler<Float, ModelsDataset> {

	@Override
	protected Float stringToValue(String text) {
		return Float.valueOf(text);
	}

	@Override
	ModelsDataset createDataset() {
		return new ModelsDataset();
	}

}

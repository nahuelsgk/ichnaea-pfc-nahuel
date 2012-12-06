package edu.upc.ichnaea.amqp.csv;

import java.util.HashMap;
import java.util.List;

import edu.upc.ichnaea.amqp.model.Dataset;

public class DatasetReader extends GenericDatasetReader<Float, Dataset> {

	@Override
	protected Float stringToValue(String text) {
		return Float.valueOf(text);
	}

	@Override
	protected Dataset createDataset(HashMap<String, List<Float>> cols) {
		return new Dataset(cols);
	}

}

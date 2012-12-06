package edu.upc.ichnaea.amqp.model;

import java.util.List;
import java.util.Map;

public class Dataset extends GenericDataset<Float> {
	
	public Dataset()
	{
		super();
	}
	
	public Dataset(Map<String, List<Float>> cols)
	{
		super(cols);
	}
}
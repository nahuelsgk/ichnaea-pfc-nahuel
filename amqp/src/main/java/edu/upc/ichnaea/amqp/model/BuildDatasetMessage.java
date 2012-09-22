package edu.upc.ichnaea.amqp.model;

public class BuildDatasetMessage {

	protected Dataset mDataset;
	
	public BuildDatasetMessage(Dataset dataset)
	{
		mDataset = dataset;
	}
	
	public Dataset getDataset()
	{
		return mDataset;
	}
}

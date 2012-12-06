package edu.upc.ichnaea.amqp.model;

public class DatasetCell {
	
	protected String mValue;
	
	DatasetCell(String value)
	{
		mValue = value;
	}
	
	DatasetCell(int value)
	{
		this(String.valueOf(value));
	}
	
	DatasetCell(float value)
	{
		this(String.valueOf(value));
	}
	
	public String toString()
	{
		return mValue;
	}
	
	public int intValue()
	{
		return Integer.parseInt(mValue);
	}

	public float floatValue()
	{
		return Float.parseFloat(mValue);
	}	
	
}

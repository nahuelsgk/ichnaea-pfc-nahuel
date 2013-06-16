package edu.upc.ichnaea.amqp.model;

public class SeasonValue implements Comparable<SeasonValue> {

	protected String mKey;
	protected String mValue;
	
	SeasonValue(String key, String value)
	{
		mKey = key;
		mValue = value;
	}
	
	SeasonValue(int key, int value)
	{
		mKey = String.valueOf(key);
		mValue = String.valueOf(value);
	}
	
	SeasonValue(float key, float value)
	{
		mKey = String.valueOf(key);
		mValue = String.valueOf(value);
	}
	
	public String toString()
	{
		return mKey+" "+mValue;
	}
	
	public String stringValue()
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
	
	public String stringKey()
	{
		return mKey;
	}
	
	public int intKey()
	{
		return Integer.parseInt(mKey);
	}

	public float floatKey()
	{
		return Float.parseFloat(mKey);
	}
	
	@Override
	public int compareTo(SeasonValue o) {
		int c = mKey.compareTo(mKey);
		if(c == 0) {
			return mValue.compareTo(o.mValue);
		} else {
			return c;
		}
	}	

}

package edu.upc.ichnaea.amqp.model;

import java.security.InvalidParameterException;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class BuildModelsFakeRequest extends BuildModelsRequest {

	protected float mDuration;
	protected float mInterval;
	
	final static String DATA_REGEX = "(?<duration>[\\d.]+):(?<interval>[\\d.]+)";
	
	public BuildModelsFakeRequest(String id, float duration, float interval) {
		super(id);
		mDuration = duration;
		mInterval = interval;
	}
	
	public BuildModelsFakeRequest(String id, String data) {
		super(id);
		Pattern pattern = Pattern.compile(DATA_REGEX);
		Matcher match = pattern.matcher(data);
		if(!match.matches()) {
			throw new InvalidParameterException("Data parameter should be in [duration:interval] format.");
		}
		mDuration = Float.parseFloat(match.group("duration"));
		mInterval = Float.parseFloat(match.group("interval"));
	}
	
	public float getDuration() {
		return mDuration;
	}
	
	public float getInterval() {
		return mInterval;
	}
	
	public String toString() {
		return String.valueOf(mDuration)+":"+String.valueOf(mInterval);
	}

}

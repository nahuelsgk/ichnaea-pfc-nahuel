package edu.upc.ichnaea.amqp.model;

public class BuildModelsFakeRequest extends BuildModelsRequest {

	protected float mDuration;
	protected float mInterval;
	
	public BuildModelsFakeRequest(String id, float duration, float interval) {
		super(id);
		mDuration = duration;
		mInterval = interval;
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

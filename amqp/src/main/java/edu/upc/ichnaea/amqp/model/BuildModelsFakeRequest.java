package edu.upc.ichnaea.amqp.model;

public class BuildModelsFakeRequest extends BuildModelsRequest {

	protected float mDuration;
	protected float mInterval;
	
	public BuildModelsFakeRequest(String id, String durationInterval) {
		super(id);
		mDuration = 0;
		mInterval = 0;		
	}
	
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

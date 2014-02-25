package edu.upc.ichnaea.amqp.model;

public class FakeRequest {

    protected String mId;
    protected float mDuration;
    protected float mInterval;

    public FakeRequest(String id, float duration, float interval) {
        mId = id;
        mDuration = duration;
        mInterval = interval;
    }
    
    public String getId() {
        return mId;
    }    

    public float getDuration() {
        return mDuration;
    }

    public float getInterval() {
        return mInterval;
    }

    public String toString() {
        return String.valueOf(mDuration) + ":" + String.valueOf(mInterval);
    }

}

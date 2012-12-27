package edu.upc.ichnaea.amqp.model;

import java.util.Calendar;

public class BuildModelsResponse {

	protected int mId;
	protected float mProgress;
	protected Calendar mStart;
	protected Calendar mEnd;
	protected byte[] mData;
	
	public BuildModelsResponse(int id, Calendar start, Calendar end, float progress) {
		this(id, start, end, null);
		mProgress = progress;		
	}
	
	public BuildModelsResponse(int id, Calendar start, Calendar end, byte[] data) {
		mId = id;
		mStart = start;
		mEnd = end;
		mData = data;
	}
	
	public int getId() {
		return mId;
	}
	
	public float getProgress() {
		return mProgress;
	}
	
	public Calendar getStart() {
		return mStart;
	}
	
	public Calendar getEnd() {
		return mEnd;
	}
	
	public byte[] getData() {
		return mData;
	}
	
}

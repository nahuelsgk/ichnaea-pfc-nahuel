package edu.upc.ichnaea.amqp.model;

import java.util.Calendar;

public class BuildModelsResponse {

	protected int mId;
	protected float mProgress;
	protected Calendar mStart;
	protected Calendar mEnd;
	protected byte[] mData;
	protected String mError;
	
	public BuildModelsResponse(int id, Calendar start, Calendar end, String error) {
		this(id, start, end);
		mError = error;
		mProgress = 1;
	}
	
	public BuildModelsResponse(int id, Calendar start, Calendar end, float progress) {
		this(id, start, end);
		mProgress = progress;		
	}
	
	public BuildModelsResponse(int id, Calendar start, Calendar end, byte[] data) {
		this(id, start, end);
		mData = data;
		mProgress = 1;
	}
	
	public BuildModelsResponse(int id, Calendar start, Calendar end) {
		mId = id;
		mStart = start;
		mEnd = end;
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

	public String getError() {
		return mError;
	}	
	
}

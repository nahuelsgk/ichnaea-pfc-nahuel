package edu.upc.ichnaea.amqp.model;

import java.util.Calendar;

public class ProgressResponse {

    protected String mId;
    protected float mProgress;
    protected Calendar mStart;
    protected Calendar mEnd;
    protected String mError;

    public ProgressResponse(String id, Calendar start, Calendar end,
            String error) {
        this(id, start, end);
        mError = error;
        mProgress = 1;
    }

    public ProgressResponse(String id, Calendar start, Calendar end,
            float progress) {
        this(id, start, end);
        mProgress = progress;
    }

    public ProgressResponse(String id, Calendar start, Calendar end) {
        mId = id;
        mStart = start;
        mEnd = end;
    }

    public boolean isEmpty() {
        return !hasStart() && !hasEnd() && !hasError();
    }

    public String getId() {
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

    public String getError() {
        return mError;
    }

    public boolean hasStart() {
        return mStart != null;
    }

    public boolean hasEnd() {
        return mEnd != null;
    }

    public boolean hasError() {
        return mError != null && !mError.isEmpty();
    }

}

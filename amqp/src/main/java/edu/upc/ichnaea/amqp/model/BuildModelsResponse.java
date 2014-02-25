package edu.upc.ichnaea.amqp.model;

import java.util.Calendar;

public class BuildModelsResponse extends ProgressResponse {

    protected byte[] mData;

    public BuildModelsResponse(String id, Calendar start, Calendar end,
            String error) {
        super(id, start, end, error);
    }

    public BuildModelsResponse(String id, Calendar start, Calendar end,
            float progress) {
        super(id, start, end, progress);
    }

    public BuildModelsResponse(String id, Calendar start, Calendar end,
            byte[] data) {
        super(id, start, end);
        mData = data;
        mProgress = 1;
    }

    public BuildModelsResponse(String id, Calendar start, Calendar end) {
        super(id, start, end);
    }

    @Override
    public boolean isEmpty() {
        return super.isEmpty() && !hasData();
    }

    public byte[] getData() {
        return mData;
    }

    public boolean hasData() {
        return mData != null && mData.length > 0;
    }

}

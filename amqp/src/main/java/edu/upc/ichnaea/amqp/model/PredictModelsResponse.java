package edu.upc.ichnaea.amqp.model;

import java.util.Calendar;

public class PredictModelsResponse extends ProgressResponse {

    protected PredictModelsResult mResult;

    public PredictModelsResponse(String id, Calendar start, Calendar end,
            String error) {
        super(id, start, end, error);
    }

    public PredictModelsResponse(String id, Calendar start, Calendar end) {
        super(id, start, end);
    }

    public PredictModelsResponse(String id, Calendar start, Calendar end,
            float progress) {
        super(id, start, end, progress);
    }

    public PredictModelsResponse(String id, Calendar start, Calendar end, float progress, PredictModelsResult result) {
        super(id, start, end, progress);
        mResult = result;
    }

    public PredictModelsResponse(String id, Calendar start, Calendar end, PredictModelsResult result) {
        super(id, start, end);
        mResult = result;
    }

    public PredictModelsResult getResult() {
        return mResult;
    }

}

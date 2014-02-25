package edu.upc.ichnaea.amqp.model;

import java.util.Calendar;

public class PredictModelsResponse extends ProgressResponse {

    public PredictModelsResponse(String id, Calendar start, Calendar end,
            String error) {
        super(id, start, end, error);
    }

    public PredictModelsResponse(String id, Calendar start, Calendar end,
            float progress) {
        super(id, start, end, progress);
    }

    public PredictModelsResponse(String id, Calendar start, Calendar end) {
        super(id, start, end);
    }

}

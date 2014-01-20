package edu.upc.ichnaea.amqp.model;

import java.util.Calendar;

public class TestModelsResponse extends AbstractProgressResponse {

    public TestModelsResponse(String id, Calendar start, Calendar end,
            String error) {
        super(id, start, end, error);
    }

    public TestModelsResponse(String id, Calendar start, Calendar end,
            float progress) {
        super(id, start, end, progress);
    }

    public TestModelsResponse(String id, Calendar start, Calendar end) {
        super(id, start, end);
    }

}

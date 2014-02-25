package edu.upc.ichnaea.amqp.model;

import java.util.Calendar;

public class FakeResponse extends ProgressResponse {

    public FakeResponse(String id, Calendar start, Calendar end,
            String error) {
        super(id, start, end, error);
    }

    public FakeResponse(String id, Calendar start, Calendar end,
            float progress) {
        super(id, start, end, progress);
    }

    public FakeResponse(String id, Calendar start, Calendar end) {
        super(id, start, end);
    }

}

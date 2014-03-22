package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.PredictModelsResponse;
import edu.upc.ichnaea.amqp.model.PredictModelsResult;

public class PredictModelsResponseHandler extends
        ProgressResponseHandler {

    byte[] mData;
    PredictModelsResponse mResponse;
    PredictModelsResult mResult;
    
    public PredictModelsResponse getData() {
        return mResponse;
    }
    
    @Override
    public void endDocument() throws SAXException {
        if (mError != null) {
            mResponse = new PredictModelsResponse(mId, mStart, mEnd, mError);
        } else if (mProgress >= 1) {
            mResponse = new PredictModelsResponse(mId, mStart, mEnd, mResult);
        } else {
            mResponse = new PredictModelsResponse(mId, mStart, mEnd, mProgress);
        }
    }
}

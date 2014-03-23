package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.FakeResponse;

public class FakeResponseHandler extends
        ProgressResponseHandler {

    byte[] mData;
    FakeResponse mResponse;
    
    public void setResponseData(byte[] data) {
        mData = data;
    }
    
    public FakeResponse getData() {
        return mResponse;
    }
    
    @Override
    public void endDocument() throws SAXException {
        if (mError != null) {
            mResponse = new FakeResponse(mId, mStart, mEnd, mError);
        } else {
            mResponse = new FakeResponse(mId, mStart, mEnd, mProgress);
        }
    }
}

package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModelsResponse;

public class BuildModelsResponseHandler extends AbstractProgressResponseHandler {

    byte[] mData;
    BuildModelsResponse mResponse;
    
    public void setResponseData(byte[] data) {
        mData = data;
    }
    
    public BuildModelsResponse getData() {
        return mResponse;
    }
    
    @Override
    public void startDocument() throws SAXException {
        mResponse = null;
        super.startDocument();
    }

    
    @Override
    public void endDocument() throws SAXException {
        if (mError != null) {
            mResponse = new BuildModelsResponse(mId, mStart, mEnd, mError);
        } else if (mProgress >= 1) {
            mResponse = new BuildModelsResponse(mId, mStart, mEnd, mData);
        } else {
            mResponse = new BuildModelsResponse(mId, mStart, mEnd, mProgress);
        }
    }
}

package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.SAXException;
import org.xml.sax.Attributes;

import edu.upc.ichnaea.amqp.model.PredictModelsResponse;
import edu.upc.ichnaea.amqp.model.PredictModelsResult;

public class PredictModelsResponseHandler extends
        ProgressResponseHandler {

    protected PredictModelsResultHandler mResultHandler;
    protected PredictModelsResult mResult;
    protected PredictModelsResponse mResponse;    
    
    public PredictModelsResponse getData() {
        return mResponse;
    }

    @Override
    public void startDocument() throws SAXException {
        super.startDocument();
        mResultHandler = null;
        mResult = null;
        mResponse = null;
    }

    @Override
    public void startElement(String uri, String localName, String qName,
            Attributes atts) throws SAXException {
        if (localName.equalsIgnoreCase(PredictModelsResultHandler.TAG_RESULT)) {
            if (mResultHandler != null) {
                throw new SAXException(
                        "A result cannot be inside another one.");
            }
            mResultHandler = new PredictModelsResultHandler();
            mResultHandler.startDocument();
        }
        if (mResultHandler != null) {
            mResultHandler.startElement(uri, localName, qName, atts);
        } else {
            super.startElement(uri, localName, qName, atts);
        }
    }

    @Override
    public void endElement(String uri, String localName, String qName)
            throws SAXException {
        if (mResultHandler != null) {
            mResultHandler.endElement(uri, localName, qName);
        } else {
            super.endElement(uri, localName, qName);
        }
        if (localName.equalsIgnoreCase(PredictModelsResultHandler.TAG_RESULT)) {
            if(mResultHandler != null) {
                mResultHandler.endDocument();
                mResult = mResultHandler.getData();
            }
            mResultHandler = null;            
        }        
    }

    @Override
    public void characters(char[] ch, int start, int length)
            throws SAXException {
        if (mResultHandler != null) {
            mResultHandler.characters(ch, start, length);
        } else {
            super.characters(ch, start, length);
        }
    }

    @Override
    public void ignorableWhitespace(char[] ch, int start, int length)
            throws SAXException {
        if (mResultHandler != null) {
            mResultHandler.ignorableWhitespace(ch, start, length);
        } else {
            super.ignorableWhitespace(ch, start, length);
        }
    }

    @Override
    public void processingInstruction(String target, String data)
            throws SAXException {
        if (mResultHandler != null) {
            mResultHandler.processingInstruction(target, data);
        } else {
            super.processingInstruction(target, data);
        }
    }

    @Override
    public void skippedEntity(String name) throws SAXException {
        if (mResultHandler != null) {
            mResultHandler.skippedEntity(name);
        } else {
            super.skippedEntity(name);
        }
    }    
    
    @Override
    public void endDocument() throws SAXException {
        if (mError != null) {
            mResponse = new PredictModelsResponse(mId, mStart, mEnd, mError);
        } else if (mResult != null) {
            mResponse = new PredictModelsResponse(mId, mStart, mEnd, mProgress, mResult);
        } else {
            mResponse = new PredictModelsResponse(mId, mStart, mEnd, mProgress);
        }
        super.endDocument();
    }
}

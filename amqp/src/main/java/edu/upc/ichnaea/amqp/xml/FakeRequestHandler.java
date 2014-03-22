package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.FakeRequest;

public class FakeRequestHandler implements ContentHandler {

    final static String TYPE = "fake";

    final static String TAG_REQUEST = "request";
    final static String ATTR_ID = "id";
    final static String ATTR_REQUEST_TYPE = "type";
    final static String ATTR_DURATION = "duration";
    final static String ATTR_INTERVAL = "interval";

    FakeRequest mRequest;
    String mId;
    float mDuration;
    float mInterval;

    public FakeRequest getData() {
        return mRequest;
    }

    @Override
    public void setDocumentLocator(Locator locator) {
    }

    @Override
    public void startDocument() throws SAXException {
        mRequest = null;
        mDuration = 0;
        mInterval = 0;
        mId = null;
    }

    @Override
    public void endDocument() throws SAXException {
        mRequest = new FakeRequest(mId, mDuration, mInterval);
    }

    @Override
    public void startPrefixMapping(String prefix, String uri)
            throws SAXException {
    }

    @Override
    public void endPrefixMapping(String prefix) throws SAXException {
    }

    @Override
    public void startElement(String uri, String localName, String qName,
            Attributes atts) throws SAXException {
        if (localName.equalsIgnoreCase(TAG_REQUEST)) {
            mId = atts.getValue(ATTR_ID);
            if (!atts.getValue(ATTR_REQUEST_TYPE).equalsIgnoreCase(TYPE)) {
                throw new SAXException("Invalid message type");
            }
            if (atts.getValue(ATTR_DURATION) != null) {
                try {
                    mDuration = Float.parseFloat(atts.getValue(ATTR_DURATION));
                } catch(Exception e) {
                }
            }
            if (atts.getValue(ATTR_INTERVAL) != null) {
                try {
                    mInterval = Float.parseFloat(atts.getValue(ATTR_INTERVAL));
                } catch(Exception e) {
                }
            }
        }
    }

    @Override
    public void endElement(String uri, String localName, String qName)
            throws SAXException {
    }

    @Override
    public void characters(char[] ch, int start, int length)
            throws SAXException {
    }

    @Override
    public void ignorableWhitespace(char[] ch, int start, int length)
            throws SAXException {
    }

    @Override
    public void processingInstruction(String target, String data)
            throws SAXException {
    }

    @Override
    public void skippedEntity(String name) throws SAXException {
    }

}
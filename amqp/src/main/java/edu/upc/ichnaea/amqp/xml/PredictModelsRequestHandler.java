package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.PredictModelsRequest;
import edu.upc.ichnaea.amqp.model.Dataset;

public class PredictModelsRequestHandler implements ContentHandler {

    final static String TYPE = "predict_models";

    final static String TAG_REQUEST = "request";
    final static String ATTR_ID = "id";
    final static String ATTR_REQUEST_TYPE = "type";

    PredictModelsRequest mRequest;
    DatasetHandler mDatasetHandler;
    Dataset mDataset;
    byte[] mData;    
    String mId;

    public PredictModelsRequest getData() {
        return mRequest;
    }

    public void setRequestData(byte[] data) {
        mData = data;
    }

    @Override
    public void setDocumentLocator(Locator locator) {
    }

    @Override
    public void startDocument() throws SAXException {
        mRequest = null;
        mDatasetHandler = null;
        mDataset = null;
        mId = null;
    }

    @Override
    public void endDocument() throws SAXException {
        mRequest = new PredictModelsRequest(mId, mDataset, mData);
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
        if (localName.equalsIgnoreCase(DatasetHandler.TAG_DATASET)) {
            if (mDatasetHandler != null) {
                throw new SAXException(
                        "A dataset cannot be inside another one.");
            }
            mDatasetHandler = new DatasetHandler();
        }
        if (mDatasetHandler != null) {
            mDatasetHandler.startElement(uri, localName, qName, atts);
        } else if (localName.equalsIgnoreCase(TAG_REQUEST)) {
            mId = atts.getValue(ATTR_ID);
            if (!atts.getValue(ATTR_REQUEST_TYPE).equalsIgnoreCase(TYPE)) {
                throw new SAXException("Invalid message type");
            }
        }
    }

    @Override
    public void endElement(String uri, String localName, String qName)
            throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.endElement(uri, localName, qName);
            if (localName.equalsIgnoreCase(DatasetHandler.TAG_DATASET)) {
                mDataset = mDatasetHandler.getDataset();
                mDatasetHandler = null;
            }
        }
    }

    @Override
    public void characters(char[] ch, int start, int length)
            throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.characters(ch, start, length);
        }
    }

    @Override
    public void ignorableWhitespace(char[] ch, int start, int length)
            throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.ignorableWhitespace(ch, start, length);
        }
    }

    @Override
    public void processingInstruction(String target, String data)
            throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.processingInstruction(target, data);
        }
    }

    @Override
    public void skippedEntity(String name) throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.skippedEntity(name);
        }
    }

}
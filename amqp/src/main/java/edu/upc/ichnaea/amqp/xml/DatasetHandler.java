package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class DatasetHandler implements ContentHandler {

    final static String TAG_DATASET = "dataset";
    final static String TAG_COLUMN = "column";
    final static String TAG_VALUE = "value";
    final static String ATTR_COLUMN_NAME = "name";

    DatasetColumn mColumn;
    StringBuilder mCharacters;
    Dataset mDataset;
    boolean mFinished;

    public Dataset getDataset() {
        if (!mFinished) {
            return null;
        }
        return mDataset;
    }

    @Override
    public void setDocumentLocator(Locator locator) {
    }

    @Override
    public void startDocument() throws SAXException {
        mDataset = null;
        mColumn = null;
        mCharacters = null;
        mFinished = false;
    }

    @Override
    public void endDocument() throws SAXException {
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
        if (mColumn != null) {
            if (!localName.equalsIgnoreCase(TAG_VALUE)) {
                throw new SAXException(
                        "Only value tags are accepted inside a column.");
            }
            mCharacters = new StringBuilder();
        } else if (mDataset != null) {
            if (!localName.equalsIgnoreCase(TAG_COLUMN)) {
                throw new SAXException(
                        "Only column tags are accepted inside a dataset.");
            }
            mColumn = new DatasetColumn(atts.getValue(ATTR_COLUMN_NAME));
        } else if (localName.equalsIgnoreCase(TAG_DATASET)) {
            mDataset = new Dataset();
        }
    }

    @Override
    public void endElement(String uri, String localName, String qName)
            throws SAXException {
        if (localName.equalsIgnoreCase(TAG_VALUE)) {
            if (mColumn == null) {
                throw new SAXException(
                        "Value tags can only be inside column tags.");
            }
            mColumn.add(mCharacters.toString());
            mCharacters = null;
        } else if (localName.equalsIgnoreCase(TAG_COLUMN)) {
            if (mDataset == null) {
                throw new SAXException(
                        "Column tags can only be inside dataset tags.");
            }
            mDataset.add(mColumn);
            mColumn = null;
        } else if (localName.equalsIgnoreCase(TAG_DATASET)) {
            mFinished = true;
        }
    }

    @Override
    public void characters(char[] ch, int start, int length)
            throws SAXException {
        if (mCharacters != null) {
            mCharacters.append(ch, start, length);
        }
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
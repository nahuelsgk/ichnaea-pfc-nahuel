package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.DatasetAging;
import edu.upc.ichnaea.amqp.model.DatasetAgingColumn;

public class DatasetAgingHandler implements ContentHandler {

	final static String TAG_AGINGS = "agings";
	final static String TAG_COLUMN = "column";
	final static String ATTR_AGING_POSITION = "position";
	final static String ATTR_COLUMN_NAME = "name";
	
	DatasetAging mAgings;
	DatasetAgingColumn mColumn;
	AgingHandler mAgingHandler;
	float mAgingPosition;
	String mColumnName;
	boolean mFinished;
	
	public DatasetAging getAgings() {
		if(!mFinished) {
			return null;
		}
		return mAgings;
	}
	
	@Override
	public void setDocumentLocator(Locator locator) {
	}
	
	@Override
	public void startDocument() throws SAXException {
		mAgings = null;
		mAgingHandler = null;
		mAgingPosition = 0;
		mColumnName = null;
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
		if(mAgingHandler != null) {
			mAgingHandler.startElement(uri, localName, qName, atts);
		} else if (mColumn != null) {
			if(!localName.equalsIgnoreCase(AgingHandler.TAG_AGING)) {
				throw new SAXException("Only aging tags are accepted inside a column.");
			}
			mAgingHandler = new AgingHandler();
			mAgingHandler.startElement(uri, localName, qName, atts);
			mAgingPosition = Float.parseFloat(atts.getValue(ATTR_AGING_POSITION));			
		} else if (mAgings != null) {
			if(!localName.equalsIgnoreCase(TAG_COLUMN)) {
				throw new SAXException("Only column tags are accepted inside dataset agings.");
			}
			mColumn = new DatasetAgingColumn();
			mColumnName = atts.getValue(ATTR_COLUMN_NAME);
		} else if (localName.equalsIgnoreCase(TAG_AGINGS)) {
			mAgings = new DatasetAging();
		}
	}

	@Override
	public void endElement(String uri, String localName, String qName)
			throws SAXException {
		if(mAgingHandler != null) {
			mAgingHandler.endElement(uri, localName, qName);		
			if(localName.equalsIgnoreCase(AgingHandler.TAG_AGING)) {
				if(mColumn == null) {
					throw new SAXException("Aging tags can only be inside column tags.");
				}
				mColumn.put(mAgingPosition, mAgingHandler.getAging());
				mAgingHandler = null;
				mAgingPosition = 0;
			}
		} else if(localName.equalsIgnoreCase(TAG_COLUMN)) {
			if(mColumn == null ) {
				throw new SAXException("Column tags can only be inside agings tags.");
			}
			mAgings.put(mColumnName, mColumn);
			mColumn = null;
			mColumnName = null;
		} else if(localName.equalsIgnoreCase(TAG_AGINGS)) {
			mFinished = true;
		}
	}

	@Override
	public void characters(char[] ch, int start, int length)
			throws SAXException {
		if(mAgingHandler != null) {
			mAgingHandler.characters(ch, start, length);
		}
	}

	@Override
	public void ignorableWhitespace(char[] ch, int start, int length)
			throws SAXException {
		if(mAgingHandler != null) {
			mAgingHandler.ignorableWhitespace(ch, start, length);
		}		
	}

	@Override
	public void processingInstruction(String target, String data)
			throws SAXException {
		if(mAgingHandler != null) {
			mAgingHandler.processingInstruction(target, data);
		}
	}

	@Override
	public void skippedEntity(String name) throws SAXException {
		if(mAgingHandler != null) {
			mAgingHandler.skippedEntity(name);
		}
	}

}

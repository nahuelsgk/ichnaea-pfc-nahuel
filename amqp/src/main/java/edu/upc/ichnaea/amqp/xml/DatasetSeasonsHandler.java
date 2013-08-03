package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.DatasetSeasons;
import edu.upc.ichnaea.amqp.model.DatasetSeasonsColumn;

public class DatasetSeasonsHandler implements ContentHandler {

	final static String TAG_SEASONS = "seasons";
	final static String TAG_COLUMN = "column";
	final static String ATTR_SEASON_POSITION = "position";
	final static String ATTR_COLUMN_NAME = "name";
	
	DatasetSeasons mSeasons;
	DatasetSeasonsColumn mColumn;
	SeasonHandler mSeasonHandler;
	float mSeasonPosition;
	String mColumnName;
	boolean mFinished;
	
	public DatasetSeasons getSeasons() {
		if(!mFinished) {
			return null;
		}
		return mSeasons;
	}
	
	@Override
	public void setDocumentLocator(Locator locator) {
	}
	
	@Override
	public void startDocument() throws SAXException {
		mSeasons = null;
		mSeasonHandler = null;
		mSeasonPosition = 0;
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
		if(mSeasonHandler != null) {
			mSeasonHandler.startElement(uri, localName, qName, atts);
		} else if (mColumn != null) {
			if(!localName.equalsIgnoreCase(SeasonHandler.TAG_SEASON)) {
				throw new SAXException("Only season tags are accepted inside a column.");
			}
			mSeasonHandler = new SeasonHandler();
			mSeasonHandler.startElement(uri, localName, qName, atts);
			mSeasonPosition = Float.parseFloat(atts.getValue(ATTR_SEASON_POSITION));			
		} else if (mSeasons != null) {
			if(!localName.equalsIgnoreCase(TAG_COLUMN)) {
				throw new SAXException("Only column tags are accepted inside dataset seasons.");
			}
			mColumn = new DatasetSeasonsColumn();
			mColumnName = atts.getValue(ATTR_COLUMN_NAME);
		} else if (localName.equalsIgnoreCase(TAG_SEASONS)) {
			mSeasons = new DatasetSeasons();
		}
	}

	@Override
	public void endElement(String uri, String localName, String qName)
			throws SAXException {
		if(mSeasonHandler != null) {
			mSeasonHandler.endElement(uri, localName, qName);		
			if(localName.equalsIgnoreCase(SeasonHandler.TAG_SEASON)) {
				if(mColumn == null) {
					throw new SAXException("Season tags can only be inside column tags.");
				}
				mColumn.put(mSeasonPosition, mSeasonHandler.getSeason());
				mSeasonHandler = null;
				mSeasonPosition = 0;
			}
		} else if(localName.equalsIgnoreCase(TAG_COLUMN)) {
			if(mColumn == null ) {
				throw new SAXException("Column tags can only be inside seasons tags.");
			}
			mSeasons.put(mColumnName, mColumn);
			mColumn = null;
			mColumnName = null;
		} else if(localName.equalsIgnoreCase(TAG_SEASONS)) {
			mFinished = true;
		}
	}

	@Override
	public void characters(char[] ch, int start, int length)
			throws SAXException {
		if(mSeasonHandler != null) {
			mSeasonHandler.characters(ch, start, length);
		}
	}

	@Override
	public void ignorableWhitespace(char[] ch, int start, int length)
			throws SAXException {
		if(mSeasonHandler != null) {
			mSeasonHandler.ignorableWhitespace(ch, start, length);
		}		
	}

	@Override
	public void processingInstruction(String target, String data)
			throws SAXException {
		if(mSeasonHandler != null) {
			mSeasonHandler.processingInstruction(target, data);
		}
	}

	@Override
	public void skippedEntity(String name) throws SAXException {
		if(mSeasonHandler != null) {
			mSeasonHandler.skippedEntity(name);
		}
	}

}

package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Season;
import edu.upc.ichnaea.amqp.model.SeasonTrial;

public class SeasonHandler implements ContentHandler {

	final static String TAG_SEASON = "season";
	final static String TAG_TRIAL = "trial";
	final static String TAG_VALUE = "value";
	final static String ATTR_KEY = "key";
	
	StringBuilder mCharacters;
	String mValueKey;	
	SeasonTrial mTrial;
	Season mSeason;
	boolean mFinished;
	
	public Season getSeason() {
		if(!mFinished) {
			return null;
		}
		return mSeason;
	}
	
	@Override
	public void setDocumentLocator(Locator locator) {
	}
	
	@Override
	public void startDocument() throws SAXException {
		mSeason = null;
		mTrial = null;
		mCharacters = null;
		mValueKey = null;
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
		if(mTrial != null) {
			if(!localName.equalsIgnoreCase(TAG_VALUE)) {
				throw new SAXException("Only value tags are accepted inside a trial.");
			}
			mCharacters = new StringBuilder();
			mValueKey = atts.getValue(ATTR_KEY);
		} else if(mSeason != null) {
			if(!localName.equalsIgnoreCase(TAG_TRIAL)) {
				throw new SAXException("Only trial tags are accepted inside a season.");
			}
			mTrial = new SeasonTrial();
		} else if (localName.equalsIgnoreCase(TAG_SEASON)) {
			mSeason = new Season();
		}
	}

	@Override
	public void endElement(String uri, String localName, String qName)
			throws SAXException {
		if(localName.equalsIgnoreCase(TAG_VALUE)) {
			if(mTrial == null) {
				throw new SAXException("Value tags can only be inside trial tags.");
			}
			mTrial.add(mValueKey, mCharacters.toString());
			mCharacters = null;
			mValueKey = null;
		} else if(localName.equalsIgnoreCase(TAG_TRIAL)) {
			if(mSeason == null ) {
				throw new SAXException("Trial tags can only be inside season tags.");
			}
			mSeason.addTrial(mTrial);
			mTrial = null;
		} else if(localName.equalsIgnoreCase(TAG_SEASON)) {
			mFinished = true;
		}
	}

	@Override
	public void characters(char[] ch, int start, int length)
			throws SAXException {
		if(mCharacters != null) {
			mCharacters.append(ch,start,length);
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

package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Aging;
import edu.upc.ichnaea.amqp.model.AgingTrial;

public class AgingHandler implements ContentHandler {

	final static String TAG_AGING = "aging";
	final static String TAG_TRIAL = "trial";
	final static String TAG_VALUE = "value";
	final static String ATTR_KEY = "key";
	
	StringBuilder mCharacters;
	String mValueKey;	
	AgingTrial mTrial;
	Aging mAging;
	boolean mFinished;
	
	public Aging getAging() {
		if(!mFinished) {
			return null;
		}
		return mAging;
	}
	
	@Override
	public void setDocumentLocator(Locator locator) {
	}
	
	@Override
	public void startDocument() throws SAXException {
		mAging = null;
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
		} else if(mAging != null) {
			if(!localName.equalsIgnoreCase(TAG_TRIAL)) {
				throw new SAXException("Only trial tags are accepted inside an aging.");
			}
			mTrial = new AgingTrial();
		} else if (localName.equalsIgnoreCase(TAG_AGING)) {
			mAging = new Aging();
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
			if(mAging == null ) {
				throw new SAXException("Trial tags can only be inside aging tags.");
			}
			mAging.addTrial(mTrial);
			mTrial = null;
		} else if(localName.equalsIgnoreCase(TAG_AGING)) {
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

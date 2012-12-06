package edu.upc.ichnaea.amqp.xml;

import javax.management.InvalidAttributeValueException;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.model.BuildModels.Season;
import edu.upc.ichnaea.amqp.model.Dataset;

public class BuildModelsHandler implements ContentHandler {

	final static String TYPE = "build_models";
	final static String SEASON_WINTER = "winter";
	final static String SEASON_SUMMER = "summer";
	
	final static String TAG_MESSAGE = "message";
	final static String ATTR_SEASON = "season";
	final static String ATTR_MESSAGE_TYPE = "type";
	
	BuildModels mData;
	Season mSeason;
	DatasetHandler mDatasetHandler;
	Dataset mDataset;
	
	public BuildModels getData() {
		return mData;
	}
	
	@Override
	public void setDocumentLocator(Locator locator) {
	}

	@Override
	public void startDocument() throws SAXException {
		mData = null;
		mSeason = null;
		mDatasetHandler = null;
		mDataset = null;
	}

	@Override
	public void endDocument() throws SAXException {
		mData = new BuildModels(mDataset, mSeason);
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
		if(localName.equalsIgnoreCase(DatasetHandler.TAG_DATASET)) {
			if(mDatasetHandler != null) {
				throw new SAXException("A dataset cannot be inside another one.");
			}
			mDatasetHandler = new DatasetHandler();
		}		
		if(mDatasetHandler != null) {
			mDatasetHandler.startElement(uri, localName, qName, atts);
		} else if(localName.equalsIgnoreCase(TAG_MESSAGE)) {
			if(!atts.getValue(ATTR_MESSAGE_TYPE).equalsIgnoreCase(TYPE)) {
				throw new SAXException("Invalid message type");
			}
			try {
				mSeason = getSeasonFromString(atts.getValue(ATTR_SEASON));
			} catch (InvalidAttributeValueException e) {
				throw new SAXException(e.getMessage());
			}
		}
	}
	
	private Season getSeasonFromString(String value) throws InvalidAttributeValueException
	{
		switch(value) {
		case SEASON_SUMMER:
			return Season.Summer;
		case SEASON_WINTER:
			return Season.Winter;
		default:
			throw new InvalidAttributeValueException("invalid season");
		}
	}
	

	@Override
	public void endElement(String uri, String localName, String qName)
			throws SAXException {
		if(mDatasetHandler != null) {
			mDatasetHandler.endElement(uri, localName, qName);
			mDataset = mDatasetHandler.getDataset();
			if(mDataset != null) {
				mDatasetHandler = null;
			}
		}		
	}

	@Override
	public void characters(char[] ch, int start, int length)
			throws SAXException {
		if(mDatasetHandler != null) {
			mDatasetHandler.characters(ch, start, length);
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

package edu.upc.ichnaea.amqp.xml;

import javax.management.InvalidAttributeValueException;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest.Season;
import edu.upc.ichnaea.amqp.model.Dataset;

public class BuildModelsRequestHandler implements ContentHandler {

	final static String TYPE = "build_models";
	final static String SEASON_WINTER = "winter";
	final static String SEASON_SUMMER = "summer";
	
	final static String TAG_REQUEST = "request";
	final static String ATTR_SEASON = "season";
	final static String ATTR_ID = "id";
	final static String ATTR_SECTION = "section";
	final static String ATTR_REQUEST_TYPE = "type";
	
	BuildModelsRequest mRequest;
	Season mSeason;
	DatasetHandler mDatasetHandler;
	Dataset mDataset;
	int mId;
	int mSection;
	
	public BuildModelsRequest getData() {
		return mRequest;
	}
	
	@Override
	public void setDocumentLocator(Locator locator) {
	}

	@Override
	public void startDocument() throws SAXException {
		mRequest = null;
		mSeason = null;
		mDatasetHandler = null;
		mDataset = null;
		mId = 0;
		mSection = 0;
	}

	@Override
	public void endDocument() throws SAXException {
		mRequest = new BuildModelsRequest(mId, mDataset, mSeason, mSection);
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
		} else if(localName.equalsIgnoreCase(TAG_REQUEST)) {
			if(!atts.getValue(ATTR_REQUEST_TYPE).equalsIgnoreCase(TYPE)) {
				throw new SAXException("Invalid message type");
			}
			try {
				mSeason = getSeasonFromString(atts.getValue(ATTR_SEASON));
			} catch (InvalidAttributeValueException e) {
				throw new SAXException(e.getMessage());
			}
			mId = Integer.parseInt(atts.getValue(ATTR_ID));
			mSection = Integer.parseInt(atts.getValue(ATTR_SECTION));
		}
	}
	
	private Season getSeasonFromString(String value) throws InvalidAttributeValueException {
		for(Season season: Season.values()) {
			if(season.toString().equalsIgnoreCase(value)) {
				return season;
			}
		}
		throw new InvalidAttributeValueException("invalid season");
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
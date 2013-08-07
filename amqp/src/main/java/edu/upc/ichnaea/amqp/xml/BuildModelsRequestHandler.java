package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModelsFakeRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetAging;

public class BuildModelsRequestHandler implements ContentHandler {

	final static String TYPE = "build_models";
	
	final static String TAG_REQUEST = "request";
	final static String ATTR_AGING = "aging";
	final static String ATTR_ID = "id";
	final static String ATTR_REQUEST_TYPE = "type";
	final static String ATTR_FAKE = "fake";
	
	BuildModelsRequest mRequest;
	DatasetHandler mDatasetHandler;
	DatasetAgingHandler mAgingHandler;
	DatasetAging mAging;
	Dataset mDataset;
	String mId;
	String mFake;
	
	public BuildModelsRequest getData() {
		return mRequest;
	}
	
	@Override
	public void setDocumentLocator(Locator locator) {
	}

	@Override
	public void startDocument() throws SAXException {
		mRequest = null;
		mDatasetHandler = null;
		mAgingHandler = null;
		mDataset = null;
		mId = null;
		mFake = null;
	}

	@Override
	public void endDocument() throws SAXException {
		if(mFake != null) {
			mRequest = new BuildModelsFakeRequest(mId, mFake);
		} else {
			mRequest = new BuildModelsRequest(mId, mDataset, mAging);
		}
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
		} else if(localName.equalsIgnoreCase(DatasetAgingHandler.TAG_AGINGS)) {
			if(mAgingHandler != null) {
				throw new SAXException("Agings cannot be inside another.");
			}
			mAgingHandler = new DatasetAgingHandler();			
		}
		if(mDatasetHandler != null) {
			mDatasetHandler.startElement(uri, localName, qName, atts);
		} else if(mDatasetHandler != null) {
			mAgingHandler.startElement(uri, localName, qName, atts);			
		} else if(localName.equalsIgnoreCase(TAG_REQUEST)) {
			mId = atts.getValue(ATTR_ID);
			if(atts.getValue(ATTR_FAKE) != null) {
				mFake = atts.getValue(ATTR_FAKE);
			} else {
				if(!atts.getValue(ATTR_REQUEST_TYPE).equalsIgnoreCase(TYPE)) {
					throw new SAXException("Invalid message type");
				}
			}
		}
	}
	
	@Override
	public void endElement(String uri, String localName, String qName)
			throws SAXException {
		if(mDatasetHandler != null) {
			mDatasetHandler.endElement(uri, localName, qName);
			if(localName.equalsIgnoreCase(DatasetHandler.TAG_DATASET)) {
				mDataset = mDatasetHandler.getDataset();
				mDatasetHandler = null;
			}
		} else if(mAgingHandler != null) {
			mAgingHandler.endElement(uri, localName, qName);
			if(localName.equalsIgnoreCase(DatasetAgingHandler.TAG_AGINGS)) {
				mAging = mAgingHandler.getAgings();
				mAgingHandler = null;
			}			
		}
	}

	@Override
	public void characters(char[] ch, int start, int length)
			throws SAXException {
		if(mDatasetHandler != null) {
			mDatasetHandler.characters(ch, start, length);
		} else if (mAgingHandler != null) {
			mAgingHandler.characters(ch, start, length);
		}
	}

	@Override
	public void ignorableWhitespace(char[] ch, int start, int length)
			throws SAXException {
		if(mDatasetHandler != null) {
			mDatasetHandler.ignorableWhitespace(ch, start, length);
		} else if (mAgingHandler != null) {
			mAgingHandler.ignorableWhitespace(ch, start, length);
		}		
	}

	@Override
	public void processingInstruction(String target, String data)
			throws SAXException {
		if(mDatasetHandler != null) {
			mDatasetHandler.processingInstruction(target, data);
		} else if (mAgingHandler != null) {
			mAgingHandler.processingInstruction(target, data);
		}		
	}

	@Override
	public void skippedEntity(String name) throws SAXException {
		if(mDatasetHandler != null) {
			mDatasetHandler.skippedEntity(name);
		} else if (mAgingHandler != null) {
			mAgingHandler.skippedEntity(name);
		}		
	}

}
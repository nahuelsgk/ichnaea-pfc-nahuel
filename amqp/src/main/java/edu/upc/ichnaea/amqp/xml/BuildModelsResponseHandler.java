package edu.upc.ichnaea.amqp.xml;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModelsResponse;

public class BuildModelsResponseHandler implements ContentHandler {

	final static String CALENDAR_FORMAT = "yyyy-MM-dd'T'HH:mm:ss.SSSZ";
	final static String TYPE = "build_models";
	
	final static String TAG_RESPONSE = "response";
	final static String ATTR_ID = "id";
	final static String ATTR_ERROR = "error";
	final static String ATTR_PROGRESS = "progress";
	final static String ATTR_START = "start";
	final static String ATTR_END = "end";
	final static String ATTR_TYPE = "type";
	
	BuildModelsResponse mResponse;
	Calendar mStart;
	Calendar mEnd;
	float mProgress;
	String mId;
	byte[] mData;
	String mError;
	SimpleDateFormat mDateFormat = new SimpleDateFormat(CALENDAR_FORMAT);	

	
	public BuildModelsResponse getData() {
		return mResponse;
	}
	
	public void setResponseData(byte[] data) {
		mData = data;
	}
	
	@Override
	public void setDocumentLocator(Locator locator) {
	}

	@Override
	public void startDocument() throws SAXException {
		mResponse = null;
		mId = null;
		mError = null;
		mStart = null;
		mEnd = null;
	}

	@Override
	public void endDocument() throws SAXException {
		if(mError != null) {
			mResponse = new BuildModelsResponse(mId, mStart, mEnd, mError);
		} else if(mProgress >= 1) {
			mResponse = new BuildModelsResponse(mId, mStart, mEnd, mData);
		} else {
			mResponse = new BuildModelsResponse(mId, mStart, mEnd, mProgress);
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
		if(localName.equalsIgnoreCase(TAG_RESPONSE)) {
			if(!atts.getValue(ATTR_TYPE).equalsIgnoreCase(TYPE)) {
				throw new SAXException("Invalid response type");
			}
			mId = atts.getValue(ATTR_ID);
			if(atts.getValue(ATTR_PROGRESS) == null) {
				mProgress = 1;
			} else {
				mProgress = Float.parseFloat(atts.getValue(ATTR_PROGRESS));
			}
			if(atts.getValue(ATTR_ERROR) != null) {
				mError = atts.getValue(ATTR_ERROR);
			}
			try {
				if(atts.getValue(ATTR_START) != null) {
					mStart = Calendar.getInstance();
					mStart.setTime(mDateFormat.parse(atts.getValue(ATTR_START)));
				}
				if(atts.getValue(ATTR_END) != null) {
					mEnd = Calendar.getInstance();
					mEnd.setTime(mDateFormat.parse(atts.getValue(ATTR_END)));
				}
			} catch (ParseException e) {
				throw new SAXException(e);
			}
		}		
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

	@Override
	public void endElement(String uri, String localName, String qName)
			throws SAXException {
		
	}

}
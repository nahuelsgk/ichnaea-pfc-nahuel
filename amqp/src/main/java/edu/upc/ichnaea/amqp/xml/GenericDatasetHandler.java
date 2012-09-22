package edu.upc.ichnaea.amqp.xml;

import java.util.ArrayList;
import java.util.List;

import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.GenericDataset;

public abstract class GenericDatasetHandler<F, D extends GenericDataset<F>> implements ContentHandler {

	final static String TAG_COLUMN = "column";
	final static String TAG_VALUE = "value";
	final static String ATTR_COLUMN_NAME = "name";
	
	List<F> mColumn;
	String mColumnName;
	StringBuilder mCharacters;
	D mDataset;
	
	public GenericDataset<F> getDataset() {
		return mDataset;
	}
	
	@Override
	public void setDocumentLocator(Locator locator) {
	}
	
	abstract D createDataset();

	@Override
	public void startDocument() throws SAXException {
		mDataset = createDataset();
		mColumn = null;
		mColumnName = null;
		mCharacters = null;
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
		if(mColumn != null) {
			if(!localName.equalsIgnoreCase(TAG_VALUE)) {
				throw new SAXException("Only value tags are accepted inside a column.");
			}
			mCharacters = new StringBuilder();
		}
		else if(localName.equalsIgnoreCase(TAG_COLUMN)) {
			mColumn = new ArrayList<F>();
			mColumnName = atts.getValue(ATTR_COLUMN_NAME);
		}
	}

	@Override
	public void endElement(String uri, String localName, String qName)
			throws SAXException {
		if(localName.equalsIgnoreCase(TAG_VALUE)) {
			if(mColumn == null) {
				throw new SAXException("Value tags can only be inside column tags.");
			}
			mColumn.add(stringToValue(mCharacters.toString()));
			mCharacters = null;
		} else if(localName.equalsIgnoreCase(TAG_COLUMN)) {
			mDataset.setColumn(mColumnName, mColumn);
			mColumn = null;
			mColumnName = null;
		}
	}
	
	protected abstract F stringToValue(String text);

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

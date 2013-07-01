package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;
import java.io.Reader;
import java.io.StringReader;


import org.xml.sax.ContentHandler;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

public class XmlReader<H extends ContentHandler> {
	
	private H mHandler;

	public XmlReader(H handler) {
		mHandler = handler;
	}
	
	public H getHandler() {
		return mHandler;
	}	
	
	public XmlReader<H> parse(String xml) throws SAXException, IOException {
		return parse(new StringReader(xml));
	}
	
	public XmlReader<H> parse(Reader reader) throws SAXException, IOException {
    	XMLReader parser = XMLReaderFactory.createXMLReader();
    	parser.setContentHandler(mHandler);
    	parser.parse(new InputSource(reader));
    	return this;
	}
}
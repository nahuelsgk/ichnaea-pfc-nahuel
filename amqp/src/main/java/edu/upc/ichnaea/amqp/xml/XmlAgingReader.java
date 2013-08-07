package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;
import java.io.Reader;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Aging;

public class XmlAgingReader extends XmlReader<AgingHandler> {

	public XmlAgingReader() {
		super(new AgingHandler());
	}
	
	public Aging getData() {
		return getHandler().getAging();
	}
	
	public Aging read(String xml) throws IOException, SAXException{
		super.parse(xml);
		return getData();
	}
	
	public Aging read(Reader reader) throws SAXException, IOException {
		super.parse(reader);
		return getData();
	}

}

package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;
import java.io.Reader;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Season;

public class XmlSeasonReader extends XmlReader<SeasonHandler> {

	public XmlSeasonReader() {
		super(new SeasonHandler());
	}
	
	public Season getData() {
		return getHandler().getSeason();
	}
	
	public Season read(String xml) throws IOException, SAXException{
		super.parse(xml);
		return getData();
	}
	
	public Season read(Reader reader) throws SAXException, IOException {
		super.parse(reader);
		return getData();
	}

}

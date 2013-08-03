package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.DatasetSeasons;

public class XmlDatasetSeasonsReader extends XmlReader<DatasetSeasonsHandler> {

	public XmlDatasetSeasonsReader() {
		super(new DatasetSeasonsHandler());
	}
	
	public DatasetSeasons getData()
	{
		return getHandler().getSeasons();
	}
	
	public DatasetSeasons read(String xml) throws SAXException, IOException {
		super.parse(xml);
		return getData();
	}

}

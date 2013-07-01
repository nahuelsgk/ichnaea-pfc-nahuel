package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;
import java.io.Reader;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Dataset;

public class XmlDatasetReader extends XmlReader<DatasetHandler> {

	public XmlDatasetReader() {
		super(new DatasetHandler());
	}
	
	public Dataset getData() {
		return getHandler().getDataset();
	}
	
	public Dataset read(String xml) throws IOException, SAXException{
		super.parse(xml);
		return getData();
	}
	
	public Dataset read(Reader reader) throws SAXException, IOException {
		super.parse(reader);
		return getData();
	}

}

package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.DatasetAging;

public class XmlDatasetAgingReader extends XmlReader<DatasetAgingHandler> {

    public XmlDatasetAgingReader() {
        super(new DatasetAgingHandler());
    }

    public DatasetAging getData() {
        return getHandler().getAgings();
    }

    public DatasetAging read(String xml) throws SAXException, IOException {
        super.parse(xml);
        return getData();
    }

}

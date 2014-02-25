package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.FakeRequest;

public class XmlFakeRequestReader extends
        XmlReader<FakeRequestHandler> {

    public XmlFakeRequestReader() {
        super(new FakeRequestHandler());
    }

    public FakeRequest getData() {
        return getHandler().getData();
    }

    public FakeRequest read(String xml) throws SAXException, IOException {
        super.parse(xml);
        return getData();
    }

}

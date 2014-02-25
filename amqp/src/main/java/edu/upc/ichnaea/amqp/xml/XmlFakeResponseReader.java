package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.FakeResponse;

public class XmlFakeResponseReader extends
        XmlReader<FakeResponseHandler> {

    public XmlFakeResponseReader() {
        super(new FakeResponseHandler());
    }

    public FakeResponse getData() {
        return getHandler().getData();
    }

    public FakeResponse read(String xml) throws SAXException,
            IOException {
        
        super.parse(xml);
        return getData();
    }

}

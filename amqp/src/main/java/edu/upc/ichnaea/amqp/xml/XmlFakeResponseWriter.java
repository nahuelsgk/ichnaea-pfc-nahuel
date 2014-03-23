package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import edu.upc.ichnaea.amqp.model.FakeResponse;

public class XmlFakeResponseWriter extends XmlProgressResponseWriter {

    public XmlFakeResponseWriter() throws ParserConfigurationException {
        super();
    }

    public XmlProgressResponseWriter build(FakeResponse resp) {
        return super.build(resp);
    }
}
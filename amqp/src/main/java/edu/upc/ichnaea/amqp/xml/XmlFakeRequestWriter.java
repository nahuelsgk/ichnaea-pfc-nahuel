package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.FakeRequest;

public class XmlFakeRequestWriter extends XmlWriter {

    public XmlFakeRequestWriter() throws ParserConfigurationException {
        super(FakeRequestHandler.TAG_REQUEST);
    }

    public XmlFakeRequestWriter build(FakeRequest data) {
        Element xmlRoot = getRoot();

        xmlRoot.setAttribute(FakeRequestHandler.ATTR_ID, String.valueOf(data.getId()));
        xmlRoot.setAttribute(FakeRequestHandler.ATTR_REQUEST_TYPE, FakeRequestHandler.TYPE);
        xmlRoot.setAttribute(FakeRequestHandler.ATTR_DURATION, String.valueOf(data.getDuration()));
        xmlRoot.setAttribute(FakeRequestHandler.ATTR_INTERVAL, String.valueOf(data.getInterval()));
       
        return this;
    }

}
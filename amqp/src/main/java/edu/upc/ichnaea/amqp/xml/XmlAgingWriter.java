package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.Aging;
import edu.upc.ichnaea.amqp.model.AgingTrial;
import edu.upc.ichnaea.amqp.model.AgingValue;

public class XmlAgingWriter extends XmlWriter {

    XmlAgingWriter() throws ParserConfigurationException {
        super(AgingHandler.TAG_AGING);
    }

    XmlAgingWriter(Document parent, Element root) {
        super(parent, root);
    }

    public XmlAgingWriter build(Aging aging) {
        Element root = getRoot();

        for (AgingTrial trial : aging) {
            Element xmlTrial = createElement(AgingHandler.TAG_TRIAL);
            for (AgingValue value : trial) {
                Element xmlValue = createElement(AgingHandler.TAG_VALUE);
                xmlValue.setAttribute(AgingHandler.ATTR_KEY, value.stringKey());
                xmlValue.setTextContent(value.stringValue());
                xmlTrial.appendChild(xmlValue);
            }
            root.appendChild(xmlTrial);
        }
        return this;
    }

}

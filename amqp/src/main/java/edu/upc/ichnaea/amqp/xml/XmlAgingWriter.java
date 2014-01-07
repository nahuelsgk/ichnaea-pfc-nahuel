package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.Aging;
import edu.upc.ichnaea.amqp.model.AgingTrial;
import edu.upc.ichnaea.amqp.model.AgingValue;

public class XmlAgingWriter extends XmlWriter {

    XmlAgingWriter() throws ParserConfigurationException {
        super("aging");
    }

    XmlAgingWriter(Document parent, Element root) {
        super(parent, root);
    }

    public XmlAgingWriter build(Aging aging) {
        Element root = getRoot();

        for (AgingTrial trial : aging) {
            Element xmlTrial = createElement("trial");
            for (AgingValue value : trial) {
                Element xmlValue = createElement("value");
                xmlValue.setAttribute("key", value.stringKey());
                xmlValue.setTextContent(value.stringValue());
                xmlTrial.appendChild(xmlValue);
            }
            root.appendChild(xmlTrial);
        }
        return this;
    }

}

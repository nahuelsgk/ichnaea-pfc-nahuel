package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.Season;
import edu.upc.ichnaea.amqp.model.SeasonTrial;
import edu.upc.ichnaea.amqp.model.SeasonValue;

public class XmlSeasonWriter extends XmlWriter {

	XmlSeasonWriter() throws ParserConfigurationException {
		super("season");
	}
	
	XmlSeasonWriter(Document parent, Element root) {
		super(parent, root);
	}	
	
	public XmlSeasonWriter build(Season season) {
		Element root = getRoot();
		
		for(SeasonTrial trial : season)
		{
			Element xmlTrial = createElement("trial");
			for(SeasonValue value : trial)
			{
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

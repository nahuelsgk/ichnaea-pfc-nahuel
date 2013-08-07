package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.DatasetAging;
import edu.upc.ichnaea.amqp.model.DatasetAgingColumn;
import edu.upc.ichnaea.amqp.model.Aging;

public class XmlDatasetAgingWriter extends XmlWriter {
	
	public XmlDatasetAgingWriter() throws ParserConfigurationException {
		super("agings");
	}
	
	public XmlDatasetAgingWriter(Document parent, Element root) {
		super(parent, root);
	}		

	public XmlDatasetAgingWriter build(DatasetAging data) {
		Element xmlRoot = getRoot();
		
		for(String colName : data.keySet())
		{
			DatasetAgingColumn col = data.get(colName);
			if(!col.isEmpty()) {
				Element xmlCol = createElement("column");
				xmlCol.setAttribute("name", colName);
				for(float agingPosition : col.keySet())
				{
					Aging aging = col.get(agingPosition);
					if(!aging.isEmpty()) {
						Element xmlAging = createElement("aging");
						xmlAging.setAttribute("position", String.valueOf(agingPosition));
						new XmlAgingWriter(getDocument(), xmlAging).build(aging);
						xmlCol.appendChild(xmlAging);
					}
				}
				xmlRoot.appendChild(xmlCol);
			}
		}
		
		return this;
	}

}
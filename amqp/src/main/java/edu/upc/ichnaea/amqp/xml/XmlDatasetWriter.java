package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetCell;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class XmlDatasetWriter extends XmlWriter {

	XmlDatasetWriter() throws ParserConfigurationException
	{
		super("dataset");
	}
	
	XmlDatasetWriter(Document parent, Element root)
	{
		super(parent, root);
	}	
	
	public XmlDatasetWriter build(Dataset dataset)
	{
		Element root = getRoot();
		
		for(DatasetColumn col : dataset)
		{			
			Element xmlCol = createElement("column");
			xmlCol.setAttribute("name", col.getName());
			for(DatasetCell cell : col)
			{
				Element xmlRow = createElement("value");
				xmlRow.setTextContent(cell.toString());
				xmlCol.appendChild(xmlRow);
			}
			root.appendChild(xmlCol);
		}
		return this;
	}
	
	

}

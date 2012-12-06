package edu.upc.ichnaea.amqp.xml;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetCell;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class DatasetBuilder extends DocumentBuilder {

	DatasetBuilder(Document doc)
	{
		super(doc, "dataset");
	}
	
	DatasetBuilder(DocumentBuilder parent, Element root)
	{
		super(parent, root);
	}	
	
	public Document build(Dataset dataset)
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
		return getDocument();
	}
	
	

}

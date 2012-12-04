package edu.upc.ichnaea.amqp.xml;

import java.util.List;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.GenericDataset;

public class GenericDatasetBuilder<F, D extends GenericDataset<F>> extends DocumentBuilder {

	GenericDatasetBuilder(Document doc)
	{
		super(doc, "dataset");
	}
	
	GenericDatasetBuilder(DocumentBuilder parent, Element root)
	{
		super(parent, root);
	}	
	
	public Document build(D dataset)
	{
		Element root = getRoot();
		
		for(String name : dataset.getColumnNames())
		{
			List<F> col = dataset.getColumn(name);			
			Element xmlCol = createElement("column");
			xmlCol.setAttribute("name", name);
			for(F row : col)
			{
				Element xmlRow = createElement("value");
				StringBuilder builder = new StringBuilder();
				builder.append(row);
				xmlRow.setTextContent(builder.toString());
				xmlCol.appendChild(xmlRow);
			}
			root.appendChild(xmlCol);
		}
		return getDocument();
	}
	
	

}

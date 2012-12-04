package edu.upc.ichnaea.amqp.xml;

import java.util.List;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.GenericDataset;

public class GenericDatasetDocument<F> extends Document {

	public GenericDatasetDocument(GenericDataset<F> dataset) throws ParserConfigurationException {
		super();
		
		Element root = createElement("dataset");
		
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
				xmlRow.setNodeValue(builder.toString());
				xmlCol.appendChild(xmlRow);
			}
			root.appendChild(xmlCol);
		}

	}

}

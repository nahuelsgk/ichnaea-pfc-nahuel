package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.DatasetSeasons;
import edu.upc.ichnaea.amqp.model.DatasetSeasonsColumn;
import edu.upc.ichnaea.amqp.model.Season;

public class XmlDatasetSeasonsWriter extends XmlWriter {
	
	public XmlDatasetSeasonsWriter() throws ParserConfigurationException {
		super("seasons");
	}
	
	public XmlDatasetSeasonsWriter(Document parent, Element root) {
		super(parent, root);
	}		

	public XmlDatasetSeasonsWriter build(DatasetSeasons data) {
		Element xmlRoot = getRoot();
		
		for(String colName : data.keySet())
		{
			DatasetSeasonsColumn col = data.get(colName);
			if(!col.isEmpty()) {
				Element xmlCol = createElement("column");
				xmlCol.setAttribute("name", colName);
				for(float seasonPosition : col.keySet())
				{
					Season season = col.get(seasonPosition);
					if(!season.isEmpty()) {
						Element xmlSeason = createElement("season");
						xmlSeason.setAttribute("position", String.valueOf(seasonPosition));
						new XmlSeasonWriter(getDocument(), xmlSeason).build(season);
						xmlCol.appendChild(xmlSeason);
					}
				}
				xmlRoot.appendChild(xmlCol);
			}
		}
		
		return this;
	}

}
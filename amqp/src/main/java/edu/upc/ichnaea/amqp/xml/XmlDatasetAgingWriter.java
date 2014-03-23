package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.DatasetAging;
import edu.upc.ichnaea.amqp.model.DatasetAgingColumn;
import edu.upc.ichnaea.amqp.model.Aging;

public class XmlDatasetAgingWriter extends XmlWriter {

    public XmlDatasetAgingWriter() throws ParserConfigurationException {
        super(DatasetAgingHandler.TAG_AGINGS);
    }

    public XmlDatasetAgingWriter(Document parent, Element root) {
        super(parent, root);
    }

    public XmlDatasetAgingWriter build(DatasetAging data) {
        Element xmlRoot = getRoot();

        for (String colName : data.keySet()) {
            DatasetAgingColumn col = data.get(colName);
            if (!col.isEmpty()) {
                Element xmlCol = createElement(DatasetAgingHandler.TAG_COLUMN);
                xmlCol.setAttribute(DatasetAgingHandler.ATTR_COLUMN_NAME, colName);
                for (float agingPosition : col.keySet()) {
                    Aging aging = col.get(agingPosition);
                    if (!aging.isEmpty()) {
                        Element xmlAging = createElement(AgingHandler.TAG_AGING);
                        xmlAging.setAttribute(DatasetAgingHandler.ATTR_AGING_POSITION,
                                String.valueOf(agingPosition));
                        new XmlAgingWriter(getDocument(), xmlAging)
                                .build(aging);
                        xmlCol.appendChild(xmlAging);
                    }
                }
                xmlRoot.appendChild(xmlCol);
            }
        }

        return this;
    }

}
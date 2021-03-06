package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetCell;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class XmlDatasetWriter extends XmlWriter {

    XmlDatasetWriter() throws ParserConfigurationException {
        super(DatasetHandler.TAG_DATASET);
    }

    XmlDatasetWriter(Document parent, Element root) {
        super(parent, root);
    }

    public XmlDatasetWriter build(Dataset dataset) {
        Element xmlRoot = getRoot();

        for (DatasetColumn col : dataset) {
            if (!col.isEmpty()) {
                Element xmlCol = createElement(DatasetHandler.TAG_COLUMN);
                xmlCol.setAttribute(DatasetHandler.ATTR_COLUMN_NAME, col.getName());
                for (DatasetCell cell : col) {
                    Element xmlRow = createElement(DatasetHandler.TAG_VALUE);
                    xmlRow.setTextContent(cell.toString());
                    xmlCol.appendChild(xmlRow);
                }
                xmlRoot.appendChild(xmlCol);
            }
        }
        return this;
    }

}

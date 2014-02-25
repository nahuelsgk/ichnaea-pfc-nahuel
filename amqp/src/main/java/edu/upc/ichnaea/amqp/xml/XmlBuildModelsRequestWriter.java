package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;

public class XmlBuildModelsRequestWriter extends XmlWriter {

    public XmlBuildModelsRequestWriter() throws ParserConfigurationException {
        super(BuildModelsRequestHandler.TAG_REQUEST);
    }

    public XmlBuildModelsRequestWriter build(BuildModelsRequest data) {
        Element xmlRoot = getRoot();

        xmlRoot.setAttribute(BuildModelsRequestHandler.ATTR_ID, String.valueOf(data.getId()));
        xmlRoot.setAttribute(BuildModelsRequestHandler.ATTR_REQUEST_TYPE, BuildModelsRequestHandler.TYPE);

        if (!data.getDataset().isEmpty()) {
            Element xmlDataset = appendChild(DatasetHandler.TAG_DATASET);
            new XmlDatasetWriter(getDocument(), xmlDataset).build(data
                    .getDataset());
        }
        if (!data.getAging().isEmpty()) {
            Element xmlAgings = appendChild(AgingHandler.TAG_AGING);
            new XmlDatasetAgingWriter(getDocument(), xmlAgings).build(data
                    .getAging());
        }

        return this;
    }

}
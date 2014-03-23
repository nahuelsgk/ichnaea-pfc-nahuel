package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.PredictModelsResult;

public class XmlPredictModelsResultReader extends
        XmlReader<PredictModelsResultHandler> {

    public XmlPredictModelsResultReader() {
        super(new PredictModelsResultHandler());
    }

    public PredictModelsResult getData() {
        return getHandler().getData();
    }

    public PredictModelsResult read(String xml) throws SAXException, IOException {
        super.parse(xml);
        return getData();
    }

}

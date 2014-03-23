package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.PredictModelsResponse;

public class XmlPredictModelsResponseReader extends
        XmlReader<PredictModelsResponseHandler> {

    public XmlPredictModelsResponseReader() {
        super(new PredictModelsResponseHandler());
    }

    public PredictModelsResponse getData() {
        return getHandler().getData();
    }

    public PredictModelsResponse read(String xml) throws SAXException,
            IOException {
        
        super.parse(xml);
        return getData();
    }

}

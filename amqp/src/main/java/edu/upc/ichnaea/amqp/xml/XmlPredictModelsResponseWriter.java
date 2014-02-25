package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import edu.upc.ichnaea.amqp.model.PredictModelsResponse;

public class XmlPredictModelsResponseWriter extends XmlProgressResponseWriter {

    public XmlPredictModelsResponseWriter() throws ParserConfigurationException {
        super();
    }
    
    public XmlProgressResponseWriter build(PredictModelsResponse resp) {
        return super.build(resp);
    }

}
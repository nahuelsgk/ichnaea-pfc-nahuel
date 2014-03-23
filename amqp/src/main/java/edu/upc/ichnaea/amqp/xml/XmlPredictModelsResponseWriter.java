package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.PredictModelsResponse;

public class XmlPredictModelsResponseWriter extends XmlProgressResponseWriter {

    public XmlPredictModelsResponseWriter() throws ParserConfigurationException {
        super(PredictModelsRequestHandler.TYPE);
    }
    
    public XmlPredictModelsResponseWriter build(PredictModelsResponse resp) {
        super.build(resp);

        if (!resp.getResult().isEmpty()) {
            Element xmlResult = appendChild(PredictModelsResultHandler.TAG_RESULT);
            new XmlPredictModelsResultWriter(getDocument(), xmlResult).build(resp
                    .getResult());
        }

        return this;
    }

}
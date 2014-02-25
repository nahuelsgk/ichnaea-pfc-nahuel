package edu.upc.ichnaea.amqp.xml;

import java.text.SimpleDateFormat;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.PredictModelsResponse;

public class XmlPredictModelsResponseWriter extends XmlWriter {

    public XmlPredictModelsResponseWriter() throws ParserConfigurationException {
        super(AbstractProgressResponseHandler.TAG_RESPONSE);
    }

    public XmlPredictModelsResponseWriter build(PredictModelsResponse resp) {
        Element root = getRoot();

        root.setAttribute(AbstractProgressResponseHandler.ATTR_ID,
                String.valueOf(resp.getId()));
        root.setAttribute(AbstractProgressResponseHandler.ATTR_TYPE,
                AbstractProgressResponseHandler.TYPE);

        if (resp.hasError()) {
            root.setAttribute(AbstractProgressResponseHandler.ATTR_ERROR,
                    resp.getError());
        }
        root.setAttribute(AbstractProgressResponseHandler.ATTR_PROGRESS,
                String.valueOf(resp.getProgress()));

        SimpleDateFormat f = new SimpleDateFormat(
                AbstractProgressResponseHandler.CALENDAR_FORMAT);
        if (resp.hasStart()) {
            root.setAttribute(AbstractProgressResponseHandler.ATTR_START,
                    f.format(resp.getStart().getTime()));
        }
        if (resp.hasEnd()) {
            root.setAttribute(AbstractProgressResponseHandler.ATTR_END,
                    f.format(resp.getEnd().getTime()));
        }
        return this;
    }

}
package edu.upc.ichnaea.amqp.xml;

import java.text.SimpleDateFormat;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.ProgressResponse;

public class XmlProgressResponseWriter extends XmlWriter {

    public XmlProgressResponseWriter() throws ParserConfigurationException {
        super(ProgressResponseHandler.TAG_RESPONSE);
    }

    public XmlProgressResponseWriter build(ProgressResponse resp) {
        Element root = getRoot();

        root.setAttribute(ProgressResponseHandler.ATTR_ID,
                String.valueOf(resp.getId()));
        root.setAttribute(ProgressResponseHandler.ATTR_TYPE,
                ProgressResponseHandler.TYPE);

        if (resp.hasError()) {
            root.setAttribute(ProgressResponseHandler.ATTR_ERROR,
                    resp.getError());
        }
        root.setAttribute(ProgressResponseHandler.ATTR_PROGRESS,
                String.valueOf(resp.getProgress()));

        SimpleDateFormat f = new SimpleDateFormat(
                ProgressResponseHandler.CALENDAR_FORMAT);
        if (resp.hasStart()) {
            root.setAttribute(ProgressResponseHandler.ATTR_START,
                    f.format(resp.getStart().getTime()));
        }
        if (resp.hasEnd()) {
            root.setAttribute(ProgressResponseHandler.ATTR_END,
                    f.format(resp.getEnd().getTime()));
        }
        return this;
    }

}
package edu.upc.ichnaea.amqp.xml;

import javax.xml.transform.OutputKeys;
import javax.xml.transform.Source;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import java.io.StringReader;
import java.io.StringWriter;

public class XmlPrettyFormatter {

    int mIndent = 4;

    public XmlPrettyFormatter() {
    }

    public String format(String input) throws IllegalArgumentException {
        Source xmlInput = new StreamSource(new StringReader(input));
        StringWriter stringWriter = new StringWriter();
        StreamResult xmlOutput = new StreamResult(stringWriter);
        TransformerFactory transformerFactory = TransformerFactory
                .newInstance();
        transformerFactory.setAttribute("indent-number", mIndent);
        try {
            Transformer transformer = transformerFactory.newTransformer();
            transformer.setOutputProperty(OutputKeys.INDENT, "yes");
            transformer.transform(xmlInput, xmlOutput);
        } catch (TransformerException e) {
            throw new IllegalArgumentException(e);
        }
        return xmlOutput.getWriter().toString();
    }

}

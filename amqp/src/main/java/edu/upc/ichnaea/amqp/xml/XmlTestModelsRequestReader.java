package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import org.xml.sax.SAXException;

import javax.mail.MessagingException;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
import javax.mail.util.ByteArrayDataSource;

import edu.upc.ichnaea.amqp.IOUtils;
import edu.upc.ichnaea.amqp.model.TestModelsRequest;

public class XmlTestModelsRequestReader extends
        XmlReader<TestModelsRequestHandler> {

    public XmlTestModelsRequestReader() {
        super(new TestModelsRequestHandler());
    }

    public TestModelsRequest getData() {
        return getHandler().getData();
    }

    public TestModelsRequest read(String mpdata) throws SAXException,
              IOException, MessagingException {

        MimeMultipart mp = new MimeMultipart(new ByteArrayDataSource(
                mpdata.getBytes(), "multipart/mixed"));
        Object content;
        if (mp.getCount() > 1) {
            content = mp.getBodyPart(1).getContent();
            getHandler().setRequestData(IOUtils.read(content));
        }
        content = mp.getBodyPart(0).getContent();
        super.parse(new String(IOUtils.read(content)));
        return getData();
    }

}

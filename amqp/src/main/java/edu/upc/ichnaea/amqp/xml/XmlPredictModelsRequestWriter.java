package edu.upc.ichnaea.amqp.xml;

import java.io.ByteArrayOutputStream;
import java.io.IOException;

import javax.activation.DataHandler;
import javax.mail.MessagingException;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
import javax.mail.util.ByteArrayDataSource;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.PredictModelsRequest;

public class XmlPredictModelsRequestWriter extends XmlWriter {

    private byte[] mData;

    public XmlPredictModelsRequestWriter() throws ParserConfigurationException {
        super(PredictModelsRequestHandler.TAG_REQUEST);
    }

    public XmlPredictModelsRequestWriter build(PredictModelsRequest req) {
        Element xmlRoot = getRoot();

        xmlRoot.setAttribute(PredictModelsRequestHandler.ATTR_ID,
                String.valueOf(req.getId()));
        xmlRoot.setAttribute(PredictModelsRequestHandler.ATTR_REQUEST_TYPE,
                PredictModelsRequestHandler.TYPE);
        
        if (!req.getDataset().isEmpty()) {
            Element xmlDataset = appendChild(DatasetHandler.TAG_DATASET);
            new XmlDatasetWriter(getDocument(), xmlDataset).build(req
                    .getDataset());
        }
        
        mData = req.getData();
        return this;
    }

    @Override
    public String toString() {
        String xml = super.toString();

        MimeMultipart mp = new MimeMultipart();
        try {
            MimeBodyPart part = new MimeBodyPart();
            part.setText(xml);
            part.setHeader("Content-Type", "text/xml");
            mp.addBodyPart(part);
            if (mData != null && mData.length > 0) {
                part = new MimeBodyPart();
                part.setDataHandler(new DataHandler(new ByteArrayDataSource(
                        mData, "")));
                part.setHeader("Content-Type", "application/zip");
                part.setHeader("Content-Transfer-Encoding", "base64");
                mp.addBodyPart(part);
            }
        } catch (MessagingException e) {
        }

        ByteArrayOutputStream out = new ByteArrayOutputStream();
        try {
            mp.writeTo(out);
            return out.toString();
        } catch (IOException | MessagingException e) {
            return "";
        }
    }

}
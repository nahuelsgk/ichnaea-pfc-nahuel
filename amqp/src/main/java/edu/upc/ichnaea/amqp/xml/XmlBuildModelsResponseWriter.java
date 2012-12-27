package edu.upc.ichnaea.amqp.xml;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.text.SimpleDateFormat;

import javax.activation.DataHandler;
import javax.mail.MessagingException;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMultipart;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.mail.Base64DataSource;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;

public class XmlBuildModelsResponseWriter extends XmlWriter {
	
	private byte[] mData;
	
	public XmlBuildModelsResponseWriter() throws ParserConfigurationException {
		super("response");
	}

	public XmlBuildModelsResponseWriter build(BuildModelsResponse resp) {
		Element root = getRoot();

		root.setAttribute(BuildModelsResponseHandler.ATTR_ID, String.valueOf(resp.getId()));
		root.setAttribute(BuildModelsResponseHandler.ATTR_TYPE, BuildModelsResponseHandler.TYPE);
		root.setAttribute(BuildModelsResponseHandler.ATTR_PROGRESS, String.valueOf(resp.getProgress()));
		
		SimpleDateFormat f = new SimpleDateFormat(BuildModelsResponseHandler.CALENDAR_FORMAT);
		if(resp.getStart() != null) {
			root.setAttribute(BuildModelsResponseHandler.ATTR_START, f.format(resp.getStart().getTime()));
		}
		if(resp.getEnd() != null) {
			root.setAttribute(BuildModelsResponseHandler.ATTR_END, f.format(resp.getEnd().getTime()));
		}
		
		mData = resp.getData();
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
			part = new MimeBodyPart();
			part.setDataHandler(new DataHandler(new Base64DataSource(mData)));
			part.setHeader("Content-Type", "application/zip");
			part.setHeader("Content-Transfer-Encoding", "base64");
			mp.addBodyPart(part);
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
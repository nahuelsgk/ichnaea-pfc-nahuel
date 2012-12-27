package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import javax.mail.MessagingException;
import javax.mail.internet.MimeMultipart;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.IOUtils;
import edu.upc.ichnaea.amqp.mail.ByteArrayDataSource;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;

public class XmlBuildModelsResponseReader extends XmlReader<BuildModelsResponseHandler> {

	public XmlBuildModelsResponseReader() {
		super(new BuildModelsResponseHandler());
	}
	
	public BuildModelsResponse getData()
	{
		return getHandler().getData();
	}
	
	public BuildModelsResponse read(String mpdata) throws SAXException, IOException, MessagingException {
		MimeMultipart mp = new MimeMultipart(new ByteArrayDataSource(mpdata.getBytes()));
		Object content = mp.getBodyPart(1).getContent();
		getHandler().setResponseData(IOUtils.read(content));
		content = mp.getBodyPart(0).getContent();
		super.parse(new String(IOUtils.read(content)));
		return getData();
	}

}

package edu.upc.ichnaea.amqp.xml;

import java.io.IOException;

import javax.mail.MessagingException;
import javax.mail.internet.MimeMultipart;

import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.mail.Base64DataSource;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;

public class XmlBuildModelsResponseReader extends XmlReader<BuildModelsResponseHandler> {

	public XmlBuildModelsResponseReader() {
		super(new BuildModelsResponseHandler());
	}
	
	public BuildModelsResponse getData()
	{
		return getHandler().getData();
	}
	
	public BuildModelsResponse read(String data) throws SAXException, IOException, MessagingException {
		
		MimeMultipart mp = new MimeMultipart(new Base64DataSource(data.getBytes()));
		
		Object content = mp.getBodyPart(0).getContent();
		
		super.parse(data);
		return getData();
	}

}

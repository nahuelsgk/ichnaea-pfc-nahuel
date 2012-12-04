package edu.upc.ichnaea.amqp.worker;

import java.io.ByteArrayInputStream;
import java.io.IOException;

import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import com.rabbitmq.client.QueueingConsumer.Delivery;

import edu.upc.ichnaea.amqp.model.BuildModelsMessage;
import edu.upc.ichnaea.amqp.xml.BuildModelsMessageHandler;
import edu.upc.ichnaea.shell.BuildModelsCommand;

public class BuildModelsWorker extends ShellCommandWorker {

	protected BuildModelsMessageHandler mHandler;
	
	private BuildModelsMessage getDeliveryMessage(Delivery delivery) throws IOException 
	{
		try{
			XMLReader parser = XMLReaderFactory.createXMLReader();
		    parser.setContentHandler(mHandler);
			parser.parse(new InputSource(new ByteArrayInputStream(delivery.getBody())));
		    return mHandler.getMessage();
		}catch(SAXException e){
			throw new IOException(e.getMessage());
		}	
	}

	@Override
	public void process(Delivery delivery) throws IOException
	{
		BuildModelsMessage msg = getDeliveryMessage(delivery);
		BuildModelsCommand cmd = new BuildModelsCommand(msg);
		runCommand(cmd);
	}
}

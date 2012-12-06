package edu.upc.ichnaea.amqp.worker;

import java.io.ByteArrayInputStream;
import java.io.IOException;

import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import com.rabbitmq.client.QueueingConsumer.Delivery;

import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.xml.BuildModelsHandler;
import edu.upc.ichnaea.shell.BuildModelsCommand;

public class BuildModelsWorker extends ShellWorker {

	protected BuildModelsHandler mHandler;
	
	private BuildModels getDeliveryMessage(Delivery delivery) throws IOException 
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
		BuildModels msg = getDeliveryMessage(delivery);
		BuildModelsCommand cmd = new BuildModelsCommand(msg);
		try {
			runCommand(cmd);
		} catch (InterruptedException e) {
			throw new IOException(e);
		}
	}
}

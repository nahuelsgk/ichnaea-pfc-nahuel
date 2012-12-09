package edu.upc.ichnaea.amqp.client;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

import edu.upc.ichnaea.amqp.csv.CsvDatasetWriter;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsRequestReader;
import edu.upc.ichnaea.shell.BuildModelsCommand;
import edu.upc.ichnaea.shell.ShellInterface;

public class BuildModelsProcessClient extends QueueClient {

	ShellInterface mShell;
	String mScriptPath;
	
	public BuildModelsProcessClient(ShellInterface shell, String scriptPath, String queue) {
		super(queue, true);
		mShell = shell;
		mScriptPath = scriptPath;
	}
	
	protected void received(Channel channel, String replyTo, byte[] body) throws IOException {
		try {
			getLogger().info("received a request");
			BuildModelsRequest request = new XmlBuildModelsRequestReader().read(new String(body));

			getLogger().info("writing dataset to a csv file");
			File datasetFile = mShell.createTempFile();
			FileOutputStream out = new FileOutputStream(datasetFile);
			new CsvDatasetWriter(new OutputStreamWriter(out)).write(request.getDataset()).close();
			
			getLogger().info("calling build models command");
			BuildModelsCommand cmd = new BuildModelsCommand(request.getSeason(), datasetFile.getAbsolutePath());
			cmd.setScriptPath(mScriptPath);
			mShell.run(cmd);
			
			if(replyTo != null) {
				getLogger().info("sending response to \""+replyTo+"\"...");
				AMQP.BasicProperties properties = new AMQP.BasicProperties().builder().
						contentType("text/xml").build();
				String responseXml = "";
				channel.basicPublish(getExchange(), replyTo, properties, responseXml.getBytes());
			}
			
			datasetFile.delete();
		} catch (InterruptedException e) {
			throw new IOException(e);			
		} catch (SAXException e) {
			throw new IOException(e);
		}
	}

	@Override
	public void run() throws IOException {
		boolean autoAck = true;
		final Channel ch = getChannel();
		ch.basicConsume(getQueue(), autoAck, new DefaultConsumer(ch) {
			@Override
			public void handleDelivery(String consumerTag,
				Envelope envelope,
                AMQP.BasicProperties properties,
                byte[] body)
                throws IOException
                {
					received(ch, properties.getReplyTo(), body);
                }
			}
		);
		
		getLogger().info("waiting for build models requests on queue \""+getQueue()+"\"...");
	}

}

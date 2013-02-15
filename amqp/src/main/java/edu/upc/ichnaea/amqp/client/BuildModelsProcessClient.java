package edu.upc.ichnaea.amqp.client;

import java.io.File;
import java.io.IOException;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.util.Calendar;
import java.util.UUID;
import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

import edu.upc.ichnaea.amqp.csv.CsvDatasetWriter;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsRequestReader;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsResponseWriter;
import edu.upc.ichnaea.shell.BuildModelsCommand;
import edu.upc.ichnaea.shell.CommandResult;
import edu.upc.ichnaea.shell.ShellInterface;

public class BuildModelsProcessClient extends QueueClient {

	ShellInterface mShell;
	String mScriptPath;
	
	public BuildModelsProcessClient(ShellInterface shell, String scriptPath, String queue) {
		super(queue, true);
		mShell = shell;
		mScriptPath = scriptPath;
	}
	
	protected void reply(Channel channel, BuildModelsResponse response, String replyTo) throws IOException, ParserConfigurationException {
		getLogger().info("sending reply to \""+replyTo+"\"...");
		AMQP.BasicProperties properties = new AMQP.BasicProperties().builder().
				contentType("multipart/mixed").build();
		String responseXml = new XmlBuildModelsResponseWriter().build(response).toString();
		channel.basicPublish(getExchange(), replyTo, properties, responseXml.getBytes());
	}
	
	protected void received(Channel channel, String replyTo, byte[] body)
			throws IOException, SAXException, InterruptedException, ParserConfigurationException {
		getLogger().info("received a request");
		Calendar start = Calendar.getInstance();
		BuildModelsRequest request = new XmlBuildModelsRequestReader().read(new String(body));

		getLogger().info("opening shell");
		mShell.open();
		
		String datasetPath = new File(mShell.getTempPath(), UUID.randomUUID().toString()).getAbsolutePath();
		
		getLogger().info("writing dataset to "+datasetPath);
		
		OutputStream out = mShell.writeFile(datasetPath);
		
		new CsvDatasetWriter(new OutputStreamWriter(out)).write(request.getDataset()).close();
		
		getLogger().info("calling build models command");
		BuildModelsCommand cmd = new BuildModelsCommand(request.getSeason(), datasetPath);
		cmd.setScriptPath(mScriptPath);
		CommandResult result = mShell.run(cmd);
		
		getLogger().info("deleting temporary dataset file");
		mShell.removeFile(datasetPath);
		
		getLogger().info("closing shell");
		mShell.close();
		
		if(replyTo != null) {
			Calendar end = Calendar.getInstance();
			byte[] data = result.getOutput().getBytes();
			int id = Integer.parseInt(replyTo);
			BuildModelsResponse response = new BuildModelsResponse(id, start, end, data);			
			reply(channel, response, replyTo);
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
					try {
						received(ch, properties.getReplyTo(), body);
					} catch (Exception e) {
						throw new IOException(e);
					}
                }
			}
		);
		
		getLogger().info("waiting for build models requests on queue \""+getQueue()+"\"...");
	}

}

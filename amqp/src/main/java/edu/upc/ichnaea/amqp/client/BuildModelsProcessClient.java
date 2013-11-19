package edu.upc.ichnaea.amqp.client;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

import com.rabbitmq.client.AMQP;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

import edu.upc.ichnaea.amqp.FileUtils;
import edu.upc.ichnaea.amqp.IOUtils;
import edu.upc.ichnaea.amqp.data.CsvDatasetWriter;
import edu.upc.ichnaea.amqp.data.AgingFolderWriter;
import edu.upc.ichnaea.amqp.model.BuildModelsFakeRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.BuildModelsResponse;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsRequestReader;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsResponseWriter;
import edu.upc.ichnaea.shell.BuildModelsCommand;
import edu.upc.ichnaea.shell.CommandResultInterface;
import edu.upc.ichnaea.shell.FakeBuildModelsCommand;
import edu.upc.ichnaea.shell.ShellInterface;

public class BuildModelsProcessClient extends Client {

	ShellInterface mShell;
	String mScriptPath;
	String mTimeFormat = "EEE MMM dd HH:mm:ss z yyyy";
	String mRegexPercentage = "(\\d*)%";
	String mRegexEndTime = "^ *finish: *(.*) *$";
	String mRequestQueue;
	String[] mResponseQueues;
	String mResponseExchange;
	
	public abstract class CommandReader {
		
		public CommandReader(CommandResultInterface result) throws IOException {
			BufferedReader in = new BufferedReader(new InputStreamReader(result.getInputStream()));
			Pattern regexPercent = Pattern.compile(mRegexPercentage);
			Pattern regexEndTime = Pattern.compile(mRegexEndTime);
			SimpleDateFormat dateFormat = new SimpleDateFormat(mTimeFormat);
			float percent = 0;
			Calendar end = null;
			String line;
			boolean updated = false;

			while((line = in.readLine()) != null) {
				updated = false;
				Matcher m = regexPercent.matcher(line);
				if(m.find()) {
					try{
						percent = Float.parseFloat(m.group(1));
					}catch(NumberFormatException e) {
						percent = 0;
					}
					if(percent > 1) {
						percent /= 100;
					}
					updated = true;
				}
				m = regexEndTime.matcher(line);
				if(m.find()) {
					end = Calendar.getInstance();
					try {
						end.setTime(dateFormat.parse(m.group(1)));
					} catch (ParseException e) {
						end = null;
					}
					updated = true;
				}
				if(updated) {
					onUpdate(percent, end);
				}
			}

			result.close();
		}
		
		protected abstract void onUpdate(float percent, Calendar end);
	};
	
	public BuildModelsProcessClient(ShellInterface shell, String scriptPath, String requestQueue, String[] responseQueues, String responseExchange) {
		mShell = shell;
		mScriptPath = scriptPath;
		mRequestQueue = requestQueue;
		mResponseQueues = responseQueues;
		mResponseExchange = responseExchange;
	}
	
	protected void sendResponse(BuildModelsResponse response, String replyTo)
			throws IOException, ParserConfigurationException {
		if(replyTo == null || replyTo.isEmpty()) {
			return;
		}
		AMQP.BasicProperties properties = new AMQP.BasicProperties().builder().
				contentType("multipart/mixed").build();
		String responseXml = new XmlBuildModelsResponseWriter().build(response).toString();
		getChannel().basicPublish(mResponseExchange, replyTo, properties, responseXml.getBytes());
	}
	
	protected void processRealRequest(BuildModelsRequest req, final String replyTo)
			throws IOException, InterruptedException, ParserConfigurationException {
		
		Calendar start = Calendar.getInstance();
		
		String err = null;
		BuildModelsResponse resp;
		
		if(req.getDataset() == null) {
			err = "No dataset received.";
		} else if(req.getAging() == null) {
			err = "No aging received.";
		}
		if(err != null) {
			resp = new BuildModelsResponse(replyTo, start, null, err);
			getLogger().warning(err);	
			sendResponse(resp, replyTo);
			return;
		}
		
		String datasetPath = FileUtils.tempPath(mShell.getTempPath());
		getLogger().info("writing dataset to "+datasetPath);
		OutputStream out = mShell.writeFile(datasetPath);
		new CsvDatasetWriter(new OutputStreamWriter(out)).write(req.getDataset()).close();
		
		String agingPath = FileUtils.tempPath(mShell.getTempPath());
		getLogger().info("writing aging to "+agingPath);
		mShell.createFolder(agingPath);
		String agingFormat = agingPath + "/env%column%-%aging%.txt";
		new AgingFolderWriter(agingFormat) {
			@Override
			protected OutputStream createFile(String path) throws IOException {
				return mShell.writeFile(path);
			}
		}.write(req.getAging());
		
		getLogger().info("calling build models command");
		BuildModelsCommand cmd = new BuildModelsCommand(datasetPath, agingPath);
		
		cmd.setScriptPath(mScriptPath);
		getLogger().info(cmd.toString());
		CommandResultInterface result = mShell.run(cmd);
		
		getLogger().info("reading command result");
		sendRequestUpdates(result, start, replyTo);
		
		getLogger().info("deleting temporary dataset file");
		mShell.removePath(datasetPath);
		
		getLogger().info("deleting temporary aging folder");
		mShell.removePath(agingPath);
		
		getLogger().info("reading output file in "+cmd.getOutputPath());
		Calendar end = Calendar.getInstance();
		
		try{
			byte[] data = IOUtils.read(mShell.readFile(cmd.getOutputPath()));
			resp = new BuildModelsResponse(replyTo, start, end, data);
		}catch(IOException e){
			err = "failed to read output file: "+e.getMessage();
			getLogger().warning(err);
			resp = new BuildModelsResponse(replyTo, start, end, err);
		}
		getLogger().info("sending result");		
		sendResponse(resp, replyTo);
	}
	
	protected void processFakeRequest(BuildModelsFakeRequest req, final String replyTo)
			throws IOException, InterruptedException, ParserConfigurationException {
		
		Calendar start = Calendar.getInstance();
		getLogger().info("calling fake build models command");
		FakeBuildModelsCommand cmd = new FakeBuildModelsCommand(req.toString());
		
		cmd.setScriptPath(mScriptPath);
		getLogger().info(cmd.toString());
		CommandResultInterface result = mShell.run(cmd);
		
		getLogger().info("reading command result");
		sendRequestUpdates(result, start, replyTo);
	}
	
	protected void sendRequestUpdates(CommandResultInterface result, final Calendar start, final String replyTo)
			throws IOException {
		new CommandReader(result){
			@Override
			protected void onUpdate(float percent, Calendar end) {
				getLogger().info("sending status update");
				try {
					sendResponse(new BuildModelsResponse(replyTo, start, end, percent), replyTo);
				} catch (IOException | ParserConfigurationException e) {
				}
			}
		};
	}
	
	protected void processRequest(final String replyTo, byte[] body)
			throws IOException, SAXException, InterruptedException, ParserConfigurationException {
		getLogger().info("received a request");
		getLogger().info(new String(body));
		
		getLogger().info("parsing the request");
		BuildModelsRequest req = new XmlBuildModelsRequestReader().read(new String(body));

		getLogger().info("opening shell");
		mShell.open();
		
		if(req instanceof BuildModelsFakeRequest) {
			getLogger().info("calling fake build models command");
			processFakeRequest((BuildModelsFakeRequest)req, replyTo);
		} else {
			processRealRequest(req, replyTo);
		}
	
		getLogger().info("closing shell");
		mShell.close();
	}
	
	@Override
	public void setup(Channel channel) throws IOException {
		super.setup(channel);
		channel.exchangeDeclare(mResponseExchange, "fanout", true);
		for(String responseQueue : mResponseQueues) {
			channel.queueDeclare(responseQueue, false, false, false, null);
			channel.queueBind(responseQueue, mResponseExchange, "");
		}
		channel.queueDeclare(mRequestQueue, false, false, false, null);
	}	
	
	@Override
	public void run() throws IOException {
		boolean autoAck = true;
		final Channel ch = getChannel();
		
		ch.basicConsume(mRequestQueue, autoAck, new DefaultConsumer(ch) {
			@Override
			public void handleDelivery(String consumerTag,
				Envelope envelope,
                AMQP.BasicProperties properties,
                byte[] body)
                throws IOException
                {
					try {
						processRequest(properties.getReplyTo(), body);
					} catch (Exception e) {
						throw new IOException(e);
					}
                }
			}
		);
		getLogger().info("waiting for build models requests on queue \""+mRequestQueue+"\"...");
	}

}

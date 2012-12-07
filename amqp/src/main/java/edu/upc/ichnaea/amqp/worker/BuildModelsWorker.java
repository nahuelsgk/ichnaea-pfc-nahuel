package edu.upc.ichnaea.amqp.worker;

import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;

import com.rabbitmq.client.QueueingConsumer.Delivery;

import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsReader;
import edu.upc.ichnaea.amqp.xml.XmlBuildModelsWriter;
import edu.upc.ichnaea.shell.BuildModelsCommand;
import edu.upc.ichnaea.shell.ShellInterface;

public class BuildModelsWorker extends ShellWorker {

	public BuildModelsWorker(ShellInterface shell) {
		super(shell);
	}

	@Override
	public void process(Delivery delivery) throws IOException {
		try {
			String datasetPath = "/tmp/lala";
			BuildModels data = new XmlBuildModelsReader().read(new String(delivery.getBody()));
			FileOutputStream out = writeFile(datasetPath);
			new XmlBuildModelsWriter().write(new OutputStreamWriter(out));
			runCommand(new BuildModelsCommand(data.getSeason(), datasetPath));			
		} catch (Exception e) {
			throw new IOException(e);
		}
	}
}

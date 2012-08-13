package edu.upc.ichnaea.amqp.worker;

import java.io.IOException;

import com.rabbitmq.client.QueueingConsumer.Delivery;

import edu.upc.ichnaea.shell.Command;
import edu.upc.ichnaea.shell.CommandResult;
import edu.upc.ichnaea.shell.ShellInterface;

abstract public class ShellCommandWorker extends Worker {

	abstract protected ShellInterface getShell();
	abstract protected Command getCommand();
	
	@Override
	public void process(Delivery delivery) throws IOException {
		try {
			getShell().run(getCommand());
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
	}

}

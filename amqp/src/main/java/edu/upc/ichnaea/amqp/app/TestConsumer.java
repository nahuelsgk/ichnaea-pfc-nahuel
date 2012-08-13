package edu.upc.ichnaea.amqp.app;

import edu.upc.ichnaea.amqp.worker.TestWorker;
import edu.upc.ichnaea.amqp.worker.WorkerInterface;


public class TestConsumer extends Consumer
{
    public static void main(String[] args)
    {
    	main(args, new TestConsumer());
    }
	
	@Override
	protected WorkerInterface createWorker()
	{
		return new TestWorker();
	}
}

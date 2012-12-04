package edu.upc.ichnaea.amqp.app;

import edu.upc.ichnaea.amqp.worker.TestWorker;
import edu.upc.ichnaea.amqp.worker.WorkerInterface;


public class TestWorkApp extends WorkApp
{
    public static void main(String[] args)
    {
    	main(args, new TestWorkApp());
    }
	
	@Override
	protected WorkerInterface createWorker()
	{
		return new TestWorker();
	}
}

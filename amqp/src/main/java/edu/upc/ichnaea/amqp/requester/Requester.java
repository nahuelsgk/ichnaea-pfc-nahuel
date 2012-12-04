package edu.upc.ichnaea.amqp.requester;

abstract class Requester implements RequesterInterface
{
	@Override
	public boolean finished()
	{
		return true;
	}
}
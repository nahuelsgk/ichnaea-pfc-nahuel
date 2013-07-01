package edu.upc.ichnaea.amqp.data;

import java.io.IOException;
import java.io.Reader;

import edu.upc.ichnaea.amqp.model.Season;

public class SeasonReader {

	public Season read(Reader reader) throws IOException
	{
		return new Season();
	}
}

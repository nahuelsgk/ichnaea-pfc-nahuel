package edu.upc.ichnaea.amqp.data;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.Reader;

import edu.upc.ichnaea.amqp.model.Season;
import edu.upc.ichnaea.amqp.model.SeasonTrial;

public class SeasonReader {

	public Season read(Reader reader) throws IOException
	{
		BufferedReader br = new BufferedReader(reader);
		Season season = new Season();
		String line;
		SeasonTrial trial = new SeasonTrial();
		while ((line = br.readLine()) != null) {
			line = line.trim();
			if(line.isEmpty()) {
				season.addTrial(trial);
				trial = new SeasonTrial();
			} else if(line.trim().charAt(0) == '#') {
				// comment
			} else {
				String words[] = line.split("\\s+");
				if(words.length != 2) {
					throw new IOException("Strange line with more than two words");
				}
				trial.add(words[0], words[1]);
			}
		}
		return season;
	}
}

package edu.upc.ichnaea.amqp.data;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.Reader;

import edu.upc.ichnaea.amqp.model.Aging;
import edu.upc.ichnaea.amqp.model.AgingTrial;

public class AgingReader {

    public Aging read(Reader reader) throws IOException {
        BufferedReader br = new BufferedReader(reader);
        Aging aging = new Aging();
        String line;
        AgingTrial trial = new AgingTrial();
        while ((line = br.readLine()) != null) {
            line = line.trim();
            if (line.isEmpty()) {
                if (trial.size() > 0) {
                    aging.addTrial(trial);
                }
                trial = new AgingTrial();
            } else if (line.trim().charAt(0) == '#') {
                // comment
            } else {
                String words[] = line.split("\\s+");
                if (words.length != 2) {
                    throw new IOException(
                            "Strange line with more than two words");
                }
                trial.add(words[0], words[1]);
            }
        }
        if (trial.size() > 0) {
            aging.addTrial(trial);
        }
        return aging;
    }
}

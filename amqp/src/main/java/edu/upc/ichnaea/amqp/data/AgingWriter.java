package edu.upc.ichnaea.amqp.data;

import java.io.IOException;
import java.io.Writer;

import edu.upc.ichnaea.amqp.model.Aging;
import edu.upc.ichnaea.amqp.model.AgingTrial;
import edu.upc.ichnaea.amqp.model.AgingValue;

public class AgingWriter {

    Writer mWriter;

    public AgingWriter(Writer writer) {
        mWriter = writer;
    }

    public AgingWriter write(Aging aging) throws IOException {

        for (AgingTrial trial : aging) {
            mWriter.write("\n");
            for (AgingValue value : trial) {
                mWriter.write(value.stringKey() + "\t" + value.stringValue()
                        + "\n");
            }
        }

        return this;
    }

    public void close() throws IOException {
        if (mWriter != null) {
            mWriter.close();
            mWriter = null;
        }
    }
}

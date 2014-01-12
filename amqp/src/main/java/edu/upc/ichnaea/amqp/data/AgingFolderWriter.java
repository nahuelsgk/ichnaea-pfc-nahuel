package edu.upc.ichnaea.amqp.data;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.util.HashMap;
import java.util.Map;

import edu.upc.ichnaea.amqp.model.DatasetAgingColumn;
import edu.upc.ichnaea.amqp.model.DatasetAging;
import edu.upc.ichnaea.amqp.model.Aging;

public abstract class AgingFolderWriter {

    static final String PlaceholderRegex = "%(.+?)%";
    protected String mFormat;
    protected Map<Float, String> mPositions;
    protected boolean mStrict = false;

    public AgingFolderWriter() {
        mFormat = "env%column%-%aging%.txt";
        mPositions = new HashMap<Float, String>();
        mPositions.put(0.5f, "Estiu");
        mPositions.put(0.0f, "Hivern");
    }

    public AgingFolderWriter(String format) {
        mFormat = format;
        mPositions = new HashMap<Float, String>();
        mPositions.put(0.5f, "Estiu");
        mPositions.put(0.0f, "Hivern");
    }

    public void setPositions(Map<Float, String> positions) {
        mPositions = positions;
    }

    abstract protected OutputStream createFile(String path) throws IOException;

    public void write(DatasetAging dataset) throws FileNotFoundException,
            IOException {
        for (String colName : dataset.keySet()) {
            DatasetAgingColumn col = dataset.get(colName);
            for (float position : col.keySet()) {
                Aging aging = col.get(position);
                String path = mFormat;
                path = path.replace("%column%", colName);
                path = path.replace("%aging%", mPositions.get(position));
                OutputStream out = createFile(path);
                new AgingWriter(new OutputStreamWriter(out)).write(aging)
                        .close();
            }
        }
    }
}

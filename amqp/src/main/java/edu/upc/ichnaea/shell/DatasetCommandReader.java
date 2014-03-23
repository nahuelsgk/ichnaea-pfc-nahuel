 
package edu.upc.ichnaea.shell;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class DatasetCommandReader extends CommandReader {

    protected Dataset mDataset;
    protected int mColumnOffset;
    protected boolean mSquare;

    public DatasetCommandReader(CommandResultInterface result, boolean verbose) {
        super(result, verbose);
        mDataset = new Dataset();
        mColumnOffset = 0;
        mSquare = false;
    }

    public DatasetCommandReader(boolean verbose) {
        super(verbose);
        mDataset = new Dataset();
        mColumnOffset = 0;
        mSquare = false;
    }

    public Dataset getDataset() {
        return mDataset;
    }

    public void setSquare(boolean square) {
        mSquare = square;
    }

    protected void onLineRead(String line) {
        if(mSquare) {
            if(!mDataset.columns().isEmpty()) {
                // remove space in normal row
                line = line.trim();
            }
        }
        boolean header = line.charAt(0) == ' ';
        String[] cells = line.split("\\s+");
        // first element is empty
		if(header) {
            mColumnOffset = mDataset.columns().size();
			for(int i = 1; i < cells.length; i++) {
				mDataset.add(new DatasetColumn(cells[i]));
			}
		} else {
            // first col is a counter
			for(int i = 1; i < cells.length; i++) {
				DatasetColumn col = mDataset.get(mColumnOffset+i-1);
				if(col != null) {
					col.add(cells[i]);
				}
			}
		}
    }

};

    
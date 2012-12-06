package edu.upc.ichnaea.amqp.csv;

import java.io.Writer;
import java.util.ArrayList;
import java.util.List;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class DatasetWriter extends CSVWriter {

	public DatasetWriter(Writer writer) {
		super(writer);
	}
	
	public DatasetWriter write(Dataset dataset) {
		int rowNum = dataset.rows().size();
		int colNum = dataset.columns().size();
		String[] row = new String[colNum];
		// store column names in a list to maintain order
		List<String> colNames = new ArrayList<String>(dataset.columnNames());
		int j=0;
		
		// write names
		for(String name : colNames) {
			row[j++] = name;
		}
		writeNext(row);
		for(int i=0; i<rowNum; i++) {
			j=0;
			row = new String[colNum];			
			for(String name: colNames) {
				String value = null;
				DatasetColumn col = dataset.get(name);
				if(col != null && col.size()>i) {
					value = col.get(i).toString();
				}
				row[j++] = value;
			}
			writeNext(row);
		}
		return this;
	}
	
}

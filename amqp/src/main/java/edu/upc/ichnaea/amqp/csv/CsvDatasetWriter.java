package edu.upc.ichnaea.amqp.csv;

import java.io.Writer;
import java.util.Collection;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class CsvDatasetWriter extends CsvWriter {

	public CsvDatasetWriter(Writer writer) {
		super(writer);
	}
	
	public CsvDatasetWriter write(Dataset dataset) {
		int rowNum = dataset.rows().size();
		int colNum = dataset.columns().size();
		String[] row = new String[colNum];
		// store column names in a list to maintain order
		Collection<String> colNames = dataset.columnNames();
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

package edu.upc.ichnaea.amqp.data;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.security.InvalidParameterException;
import java.util.HashMap;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import edu.upc.ichnaea.amqp.model.DatasetAgingColumn;
import edu.upc.ichnaea.amqp.model.DatasetAging;
import edu.upc.ichnaea.amqp.model.Aging;

public class AgingFolderReader {

	static final String PlaceholderRegex = "%(.+?)%";
	protected String mFormat;
	protected Map<String,Float> mPositions;
	protected boolean mStrict = false;
	
	public AgingFolderReader() {
		mFormat = "env%column%-%name%.txt";
		mPositions = new HashMap<String,Float>();
		mPositions.put("Summer", 0.5f);
		mPositions.put("Estiu", 0.5f);
		mPositions.put("Winter", 0.0f);
		mPositions.put("Hivern", 0.0f);
	}	
	
	public AgingFolderReader(String format, Map<String,Float> positions) {
		mFormat = format;
		mPositions = positions;
	}
	
	public DatasetAging read(File folder) throws FileNotFoundException, IOException {
		DatasetAging agings = new DatasetAging();
		String regex = mFormat.replaceAll(PlaceholderRegex, "(?<$1>.+?)");
		Pattern pattern = Pattern.compile(regex, Pattern.CASE_INSENSITIVE);
		for(File file : folder.listFiles()) {
			Matcher match = pattern.matcher(file.getName());
			if(match.matches()) {		
				String col = match.group("column");
				String agingName = match.group("aging");
				if(!mPositions.containsKey(agingName)) {
					if(mStrict) {
						throw new InvalidParameterException("Could not find aging with name '"+agingName+"' in positions.");
					} else {
						continue;
					}
				}
				Aging aging = new AgingReader().read(new FileReader(file));
				DatasetAgingColumn agingCol = agings.get(col);
				if(agingCol == null) {
					agingCol = new DatasetAgingColumn();
				}
				agingCol.put(mPositions.get(agingName), aging);
				agings.put(col, agingCol);
			}
		}
		return agings;
	}
}

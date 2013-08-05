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

import edu.upc.ichnaea.amqp.model.DatasetSeasonsColumn;
import edu.upc.ichnaea.amqp.model.DatasetSeasons;
import edu.upc.ichnaea.amqp.model.Season;

public class SeasonsFolderReader {

	static final String PlaceholderRegex = "%(.+?)%";
	protected String mFormat;
	protected Map<String,Float> mPositions;
	protected boolean mStrict = false;
	
	public SeasonsFolderReader() {
		mFormat = "env%column%-%season%.txt";
		mPositions = new HashMap<String,Float>();
		mPositions.put("Summer", 0.5f);
		mPositions.put("Estiu", 0.5f);
		mPositions.put("Winter", 0.0f);
		mPositions.put("Hivern", 0.0f);
	}	
	
	public SeasonsFolderReader(String format, Map<String,Float> positions) {
		mFormat = format;
		mPositions = positions;
	}
	
	public DatasetSeasons read(File folder) throws FileNotFoundException, IOException {
		DatasetSeasons seasons = new DatasetSeasons();
		String regex = mFormat.replaceAll(PlaceholderRegex, "(?<$1>.+?)");
		Pattern pattern = Pattern.compile(regex, Pattern.CASE_INSENSITIVE);
		for(File file : folder.listFiles()) {
			Matcher match = pattern.matcher(file.getName());
			if(match.matches()) {		
				String col = match.group("column");
				String seasonName = match.group("season");
				if(!mPositions.containsKey(seasonName)) {
					if(mStrict) {
						throw new InvalidParameterException("Could not find season with name '"+seasonName+"' in positions.");
					} else {
						continue;
					}
				}
				Season season = new SeasonReader().read(new FileReader(file));
				DatasetSeasonsColumn colSeasons = seasons.get(col);
				if(colSeasons == null) {
					colSeasons = new DatasetSeasonsColumn();
				}
				colSeasons.put(mPositions.get(seasonName), season);
				seasons.put(col, colSeasons);
			}
		}
		return seasons;
	}
}

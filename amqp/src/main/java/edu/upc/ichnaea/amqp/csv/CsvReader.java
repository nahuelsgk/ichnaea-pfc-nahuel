package edu.upc.ichnaea.amqp.csv;

import java.io.IOException;
import java.io.Reader;
import java.util.List;

public class CsvReader {

	protected char mSeparator = ';';
	protected char mQuote = '"';
	protected char mEscape = '\\';
	protected int mStartLine = 0;
	protected boolean mStrictQuotes = true;
	protected boolean mIgnoreWhiteSpace = true; 
	
	public void setSeparator(char sep)
	{
		mSeparator = sep;
	}
	
	public void setQuote(char quote)
	{
		mQuote = quote;
	}
	
	public void setEscape(char escape)
	{
		mEscape = escape;
	}
	
	public void setStartLine(int line)
	{
		mStartLine = line;
	}
	
	public void setStrictQuotes(boolean strict)
	{
		mStrictQuotes = strict;
	}
	
	protected au.com.bytecode.opencsv.CSVReader createReader(Reader reader)
	{
		return new au.com.bytecode.opencsv.CSVReader(reader, mSeparator, mQuote, mEscape, mStartLine);
	}
	
	protected List<String[]> readRows(Reader reader) throws IOException 
	{
		au.com.bytecode.opencsv.CSVReader csvReader = createReader(reader); 
		List<String[]> rows = csvReader.readAll();
		csvReader.close();
		return rows;
	}
	
	
}

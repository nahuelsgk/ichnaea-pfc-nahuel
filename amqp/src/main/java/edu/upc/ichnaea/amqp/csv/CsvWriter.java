package edu.upc.ichnaea.amqp.csv;

import java.io.IOException;
import java.io.Writer;
import java.util.List;

public class CsvWriter {
	
	Writer mWriter;
	au.com.bytecode.opencsv.CSVWriter mCsvWriter;
	
	protected char mSeparator = ';';
	protected char mQuote = '"';
	protected char mEscape = '\\';
	protected String mLineEnd = "\n";

	public CsvWriter(Writer writer)
	{
		mWriter = writer;
	}
	
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
	
	public void setLineEnd(String lineEnd)
	{
		mLineEnd = lineEnd;
	}
	
	protected au.com.bytecode.opencsv.CSVWriter getWriter()
	{
		if(mCsvWriter == null)
		{
			mCsvWriter = new au.com.bytecode.opencsv.CSVWriter(mWriter, mSeparator, mQuote, mEscape, mLineEnd);
		}
		return mCsvWriter;
	}
	
	protected void writeAll(List<String[]> rows)
	{
		getWriter().writeAll(rows);
	}
	
	protected void writeNext(String[] row)
	{
		getWriter().writeNext(row);
	}
	public void close() throws IOException
	{
		if(mCsvWriter != null)
		{
			mCsvWriter.close();
			mCsvWriter = null;
		}
	}
}

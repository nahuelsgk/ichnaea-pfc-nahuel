package edu.upc.ichnaea.amqp.mail;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import javax.activation.DataSource;

public class ByteArrayDataSource implements DataSource {
	
	private byte[] mData;
	private String mContentType = "text/plain";
	private String mName = null;

	public ByteArrayDataSource() {
	}
	
	public ByteArrayDataSource(byte[] data) {
		mData = data;
	}	
	
	public ByteArrayDataSource(byte[] data, String contentType) {
		mData = data;
		mContentType = contentType;
	}
	
	public ByteArrayDataSource(byte[] data, String contentType, String name) {
		mData = data;
		mContentType = contentType;
		mName = name;
	}
	
	public byte[] getData() {
		return mData;
	}

	@Override
	public String getContentType() {
		return mContentType;
	}

	@Override
	public InputStream getInputStream() throws IOException {
	    return new ByteArrayInputStream(mData);
	}

	@Override
	public String getName() {
		return mName;
	}

	@Override
	public OutputStream getOutputStream() throws IOException {
		
		return new ByteArrayOutputStream() {

			@Override
			public void close() throws IOException {
				super.close();
				mData = toByteArray();
			}
		};
		
	}

}

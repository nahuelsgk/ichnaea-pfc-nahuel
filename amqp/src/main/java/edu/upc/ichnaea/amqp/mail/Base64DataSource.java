package edu.upc.ichnaea.amqp.mail;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

import javax.mail.MessagingException;
import javax.mail.internet.MimeUtility;

import javax.activation.DataSource;

public class Base64DataSource implements DataSource {
	
	private byte[] mData;
	private String mContentType = "text/plain";
	private String mName = null;

	public Base64DataSource() {
	}
	
	public Base64DataSource(byte[] data) {
		mData = data;
	}	
	
	public Base64DataSource(byte[] data, String contentType) {
		mData = data;
		mContentType = contentType;
	}
	
	public Base64DataSource(byte[] data, String contentType, String name) {
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
		ByteArrayOutputStream baos = new ByteArrayOutputStream();
	    OutputStream b64os;
		try {
			b64os = MimeUtility.encode(baos, "base64");
		} catch (MessagingException e) {
			throw new IOException(e);
		}
	    b64os.write(mData);
	    b64os.close();
	    baos.close();
	    return new ByteArrayInputStream(baos.toByteArray());
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
				byte[] b = toByteArray();
		        ByteArrayInputStream bais = new ByteArrayInputStream(b);
				try {
					InputStream b64is = MimeUtility.decode(bais, "base64");
					byte[] tmp = new byte[b.length];
					int n = b64is.read(tmp);
			        b64is.close();
			        mData = new byte[n];
			        System.arraycopy(tmp, 0, mData, 0, n);
				} catch (MessagingException e) {
				}
		        bais.close();
			}
		};
		
	}

}

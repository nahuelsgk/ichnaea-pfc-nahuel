package edu.upc.ichnaea.amqp.mail;

import java.io.IOException;
import java.io.OutputStream;

import org.junit.*;

import edu.upc.ichnaea.amqp.IOUtils;

import static org.junit.Assert.*;

public class Base64DataSourceTest {

	@Test
	public void testStringInput() throws IOException {
		Base64DataSource source = new Base64DataSource(new String("test").getBytes());
		
		String result = new String(IOUtils.read(source.getInputStream()));
		String expectedResult = "dGVzdA==";
		
		assertEquals(expectedResult, result);
	}
	
	@Test
	public void testBinaryInput() throws IOException {
		byte[] data = { 't','e','s','t', 0, 't', 'e', 's', 't' };
		Base64DataSource source = new Base64DataSource(data);
		
		String result = new String(IOUtils.read(source.getInputStream()));
		String expectedResult = "dGVzdAB0ZXN0";
		
		assertEquals(expectedResult, result);
	}

	@Test
	public void testStringOutput() throws IOException {
		Base64DataSource source = new Base64DataSource();
		
		OutputStream out = source.getOutputStream();
		out.write(new String("dGVzdA==").getBytes());
		out.close();
		
		String result = new String(source.getData());
		String expectedResult = "test";
		
		assertEquals(expectedResult, result);
	}
}

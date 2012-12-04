package edu.upc.ichnaea.amqp.xml;

import java.io.StringWriter;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.w3c.dom.Element;
import org.w3c.dom.Node;

public class Document {
	
	protected org.w3c.dom.Document mDocument;
	
	public Document() throws ParserConfigurationException
	{
		DocumentBuilderFactory docFactory = DocumentBuilderFactory.newInstance();
		DocumentBuilder docBuilder = docFactory.newDocumentBuilder();
		mDocument = docBuilder.newDocument();
	}
	
	public void appendChild(Node node)
	{
		mDocument.appendChild(node);
	}
	
	public Element createElement(String name)
	{
		return mDocument.createElement(name);
	}
	
	public String toString()
	{
		try{
			TransformerFactory transformerFactory = TransformerFactory.newInstance();
			Transformer transformer = transformerFactory.newTransformer();
			DOMSource source = new DOMSource(mDocument);
	
			StringWriter outWriter = new StringWriter();  
			StreamResult result = new StreamResult( outWriter );
			transformer.transform(source, result);
			return outWriter.toString();
		}catch(TransformerException e){
			return e.getMessage();
		}
		
	}
		
}

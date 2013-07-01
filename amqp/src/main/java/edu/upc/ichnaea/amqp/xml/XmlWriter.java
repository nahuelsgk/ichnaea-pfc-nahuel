package edu.upc.ichnaea.amqp.xml;

import java.io.StringWriter;
import java.io.Writer;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.w3c.dom.DOMException;
import org.w3c.dom.Document;
import org.w3c.dom.Element;

public class XmlWriter {
	
	Document mDocument;
	Element mRoot;

	public XmlWriter(Document doc, Element root) {
		mDocument = doc;
		mRoot = root;
	}
	
	public XmlWriter(Document doc, String rootName) {
		this(doc, doc.createElement(rootName));
		doc.appendChild(mRoot);
	}
	
	public XmlWriter(String rootName) throws ParserConfigurationException {
		this(createDocument(), rootName);
	}
	
	protected Document getDocument() {
		return mDocument;
	}
	
	protected Element getRoot() {
		return mRoot;
	}
	
	protected Element createElement(String tagName) throws DOMException {
		return getDocument().createElement(tagName);
	}
	
	protected Element appendChild(String tagName) {
		return appendChild(createElement(tagName));
	}
	
	protected Element appendChild(Element child) {
		getRoot().appendChild(child);
		return child;
	}
	
	protected static Document createDocument() throws ParserConfigurationException {
		DocumentBuilderFactory docFactory = DocumentBuilderFactory.newInstance();
		DocumentBuilder docBuilder = docFactory.newDocumentBuilder();
		return docBuilder.newDocument();
	}
	
	public void write(Writer writer) throws TransformerException {
		TransformerFactory transformerFactory = TransformerFactory.newInstance();
		Transformer transformer = transformerFactory.newTransformer();
		transformer.setOutputProperty(OutputKeys.OMIT_XML_DECLARATION, "yes");
		
		DOMSource source = new DOMSource(getDocument());
  
		StreamResult result = new StreamResult( writer );
		transformer.transform(source, result);
	}
	
	public String toString() {
		StringWriter writer = new StringWriter();
		try {
			write(writer);
			return writer.toString();			
		} catch (TransformerException e) {
			return e.getMessage();
		}
	}
}

package edu.upc.ichnaea.amqp.xml;

import org.w3c.dom.Element;

/**
 * Abstract class used to build an xml document.
 * 
 * @author mibero
 *
 */
abstract public class DocumentBuilder {
	
	private Document mDocument;
	private Element mRoot;
	
	DocumentBuilder(DocumentBuilder parent, Element root)
	{
		this(parent.mDocument, root);
	}
	
	DocumentBuilder(DocumentBuilder parent)
	{
		this(parent.mDocument, parent.mRoot);
	}	
	
	DocumentBuilder(Document doc, Element root)
	{
		mDocument = doc;
		mRoot = root;
	}
	
	DocumentBuilder(Document doc, String rootTag)
	{
		mDocument = doc;
		mRoot = mDocument.createElement(rootTag);
		mDocument.appendChild(mRoot);
	}
	
	protected Element appendChild(Element child)
	{
		getRoot().appendChild(child);
		return child;
	}
	
	protected Element appendChild(String tag)
	{
		return appendChild(createElement(tag));
	}
	
	protected Element createElement(String tag)
	{
		return mDocument.createElement(tag);
	}
	
	protected Element getRoot()
	{
		return mRoot;
	}
	
	protected Document getDocument()
	{
		return mDocument;
	}

}

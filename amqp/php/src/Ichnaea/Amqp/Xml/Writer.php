<?php

namespace Ichnaea\Amqp\Xml;

abstract class Writer
{
	private $document;
	private $root;

	public function __construct($document=null, $root="document")
	{
		if (is_string($document)) {
			$root = $document;
		}
		if(!$document instanceof \DOMDocument) {
			$document = new \DOMDocument();
		}
		$this->document = $document;
		if (is_string($root)) {
			$root = $document->createElement($root);
			$document->appendChild($root);
		}
		$this->root = $root;
	}

	public function __toString()
	{
		return $this->document->saveXML();
	}

	protected function getDocument()
	{
		return $this->document;
	}

	protected function getRoot()
	{
		return $this->root;
	}

	protected function createElement($name, $value="")
	{
		return $this->document->createElement($name, $value);
	}
	
	protected function appendChild($child, $value="")
	{
		if (is_string($child)) {
			$child = $this->createElement($child, $value);
		}
		return $this->root->appendChild($child);
	}
}
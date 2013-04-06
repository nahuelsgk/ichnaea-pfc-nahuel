<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\Dataset;

class DatasetWriter extends Writer
{
	public function __construct($document=null, $root="dataset")
	{
		parent::__construct($document, $root);
	}

	public function build(Dataset $dataset)
	{
		$xmlRoot = $this->getRoot();
		foreach ($dataset as $name=>$values) {
			$xmlCol = $this->createElement("column");
			$xmlRoot->appendChild($xmlCol);
			$xmlCol->setAttribute("name", $name);
			foreach($values as $value) {
				$xmlCol->appendChild($this->createElement("value", $value));
			}
		}
	}
}
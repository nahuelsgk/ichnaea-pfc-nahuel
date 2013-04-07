<?php

namespace Ichnaea\Amqp\Xml;

use Ichnaea\Amqp\Model\BuildModelsRequest;

class BuildModelsRequestWriter extends Writer
{
	public function __construct($document=null, $root="request")
	{
		parent::__construct($document, $root);
	}

	public function build(BuildModelsRequest $req)
	{
		$xmlRoot = $this->getRoot();
		$xmlRoot->setAttribute("id", $req->getId());
		$xmlRoot->setAttribute("type", "build_models");

		if (!$req->isFake()) {
			$xmlRoot->setAttribute("section", $req->getSection());
			$xmlRoot->setAttribute("season", $req->getSeason());
			$xmlDataset = $this->createElement("dataset");
			$xmlRoot->appendChild($xmlDataset);
			$writer = new DatasetWriter($this->getDocument(), $xmlDataset);
			$writer->build($req->getDataset());
		} else {
			$xmlRoot->setAttribute("fake", $req->getFake());
		}
	}
}
<?php

namespace Ichnaea\Amqp\Model;
      

class BuildModelsRequest
{
	const Summer = "summer";
    const Winter = "winter";

    private $id;
    private $section;
    private $season;
    private $dataset;
    private $fake;

	public function __construct($id=null)
	{
		if (!$id) {
			$id = uniqid();
		}
		$this->id = $id;
		$this->dataset = new Dataset();
	}

	public function setDataset($dataset)
	{
		if (!$dataset instanceof Dataset) {
			$dataset = new Dataset($dataset);
		}
		$this->dataset = $dataset;
	}

	public function setSeason($season)
	{
		$seasons = array(self::Summer, self::Winter);
		if (!in_array($season, $seasons)) {
			throw new \InvalidArgumentException("Invalid season");
		}
		$this->season = $season;
	}

	public function setSection($section)
	{
		$this->section = intval($section);
	}

	/**
	 * Fake sets a fake request. The format should be
	 * a string of type "T:I", where T is the total time
	 * the fake request should take and I is the interval
	 * in which the response will be updated
	 */
	public function setFake($fake)
	{
		$this->fake = $fake;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getSeason()
	{
		return $this->season;
	}

	public function getSection()
	{
		return $this->section;
	}

	public function getDataset()
	{
		return $this->dataset;
	}

	public function getFake()
	{
		return $this->fake;
	}

	public function isFake()
	{
		return $this->fake != null;
	}

	public function toArray()
	{
		return array(
			"id"		=> $this->id,
			"season"	=> $this->season,
			"section"	=> $this->section,
			"fake"		=> $this->fake,
			"dataset"	=> $this->dataset->toArray()
		);
	}

	public function update(array $data)
	{
		if (array_key_exists('dataset', $data)) {
			$this->setDataset($data['dataset']);
		}
		if (array_key_exists('season', $data)) {
			$this->setSeason($data['season']);
		}
		if (array_key_exists('section', $data)) {
			$this->setSection($data['section']);
		}
		if (array_key_exists('fake', $data)) {
			$this->setFake($data['fake']);
		}
		if (array_key_exists('fake_duration', $data)
			|| array_key_exists('fake_interval', $data)) {
			$this->setFake($data['fake_duration'].":".$data['fake_interval']);	
		}
	}

	static function fromArray(array $data)
	{
		$data = array_merge(array('id'=>null), $data);
		$req = new self($data['id']);
		$req->update($data);
		return $req;
	}

}
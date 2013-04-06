<?php

namespace Ichnaea\Amqp\Model;
      
class BuildModelsResponse
{
	const DateTimeFormat = "c";
	private $id;
	private $progress = 0;
	private $start = null;
	private $end = null;
	private $data = null;
	private $error = null;

	public function __construct($id=null)
	{
		if (!$id) {
			$id = uniqid();
		}
		$this->id = $id;
		$this->setStart("now");
	}

	public function setProgress($p)
	{
		$this->progress = floatval($p);
	}

	public function setStart($start)
	{
		if(is_string($start)) {
			$start = strtotime($start);
		}
		$this->start = new \DateTime();
		$this->start->setTimestamp($start);
	}

	public function setEnd($end)
	{
		if(is_string($end)) {
			$end = strtotime($end);
		}
		$this->end = new \DateTime();
		$this->end->setTimestamp($end);
	}

	public function setError($err)
	{
		$this->error = $err;
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function getId()
	{
		return $this->id;
	}
	
	public function getProgress()
	{
		return $this->progress;
	}
	
	public function getStart()
	{
		return $this->start;
	}
	
	public function getEnd()
	{
		return $this->end;
	}
	
	public function getData()
	{
		return $this->data;
	}

	public function getError()
	{
		return $this->error;
	}

	public function hasError()
	{
		return $this->error != null;
	}

	public function hasEnd()
	{
		return $this->end != null;
	}

	public function toArray()
	{
		$a = array(
			"id"		=> $this->id,
			"progress"	=> $this->progress,
			"start"		=> $this->start,
			"end"		=> $this->end,			
			"error"		=> $this->error,
			"data"		=> $this->data
		);
		if($a['start'] instanceof \DateTime) {
			$a['start'] = $a['start']->format(self::DateTimeFormat);
		}
		if($a['end'] instanceof \DateTime) {
			$a['end'] = $a['end']->format(self::DateTimeFormat);
		}
		return $a;
	}

	public function update(array $data)
	{
		if (array_key_exists('progress', $data)) {
			$this->setProgress($data['progress']);
		}
		if (array_key_exists('start', $data)) {
			$this->setStart($data['start']);
		}
		if (array_key_exists('end', $data)) {
			$this->setEnd($data['end']);
		}
		if (array_key_exists('error', $data)) {
			$this->setError($data['error']);
		}
		if (array_key_exists('data', $data)) {
			$this->setData($data['data']);
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
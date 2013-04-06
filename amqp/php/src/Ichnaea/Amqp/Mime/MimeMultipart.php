<?php

namespace Ichnaea\Amqp\Mime;

class MimeMultipart
{
	const BoundaryMark = "--";
	const EndOfLine = "\r\n";
	private $boundary;
	private $parts = array();

	public function __construct($boundary=null)
	{
		if(!$boundary)
		{
			$boundary = uniqid();
		}
		$this->boundary = $boundary;
	}

	public function setBoundary($boundary)
	{
		$this->boundary = $boundary;
	}

	public function getBoundary()
	{
		return $this->boundary;
	}

	public function getParts()
	{
		return $this->parts;
	}

	public function getPart($n)
	{
		if (array_key_exists($n, $this->parts)) {
			return $this->parts[$n];
		}
	}

	public function getNumParts()
	{
		return count($this->parts);
	}

	public function addPart(MimePart $part)
	{
		$this->parts[] = $part;
	}

	public function __toString()
	{
		$str = "";
		foreach ($this->parts as $part) {
			$str .= BoundaryMark.$this->boundary."\n";
			$str .= $part."\n";
		}
		$str .= BoundaryMark.$this->boundary.BoundaryMark."\n";
		return $str;
	}

	public static function fromString($data)
	{
		$multi = new MimeMultipart();
		$lines = explode(self::EndOfLine, $data);
		$part = "";
		$start = true;
		$n = strlen(self::BoundaryMark);
		foreach ($lines as $line) {
			if (substr($line, 0, $n) === self::BoundaryMark) {
				$boundary = substr($line, $n);
				if ($start) {
					$multi->setBoundary($boundary);
					$start = false;
					continue;
				}
				if ($boundary === $multi->getBoundary().self::BoundaryMark) {
					$multi->addPart(MimePart::fromString($part));
					break;
				}
				if ($boundary === $multi->getBoundary()) {
					$multi->addPart(MimePart::fromString($part));
					$part = "";
					continue;
				}
			}
			$part .= $line.self::EndOfLine;
		}
		return $multi;
	}
}
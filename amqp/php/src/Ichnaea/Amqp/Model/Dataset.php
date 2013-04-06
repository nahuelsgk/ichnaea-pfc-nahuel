<?php

namespace Ichnaea\Amqp\Model;

class Dataset implements \IteratorAggregate
{
    const CsvDelimiter = ";";	
	private $columns = array();

	public function __construct($data=null)
	{
		if ($data instanceof \SplFileInfo) {
			$data = $data->openFile("r");
		}
		if ($data instanceof \SplFileObject) {
			$rows = array();
			while (!$data->eof()) {
    			$rows[] = $data->fgetcsv(self::CsvDelimiter);
			}
		}
		if (is_string($data)) {
			$rows = str_getcsv($data, self::CsvDelimiter);
		}
		if (isset($rows)) {
			$this->setRows($rows, true);
		}
		if (is_array($data)) {
			$this->setColumns($data);
		}
	}

	public function setColumns(array $cols)
	{
		$this->columns = $cols;
	}

	public function setColumnNames(array $names)
	{
		$oldNames = array_keys($this->columns);
		foreach ($names as $n=>$name) {
			if (isset($oldNames[$n])) {
				$this->columns[$name] = $this->columns[$oldNames[$n]];
				unset($this->columns[$oldNames[$n]]);
			} else {
				$this->columns[$name] = array();
			}
		}
	}

	public function setRows(array $rows, $withNames=false)
	{
		$rows = array_values($rows);
		if ($withNames) {
			$this->setColumnNames($rows[0]);
			$rows = array_slice($rows, 1);
		}
		$names = $this->getColumnNames();

		foreach ($rows as $r=>$row) {
			if (!is_array($row)) {
				continue;
			}
			foreach($row as $c=>$value) {
				$this->columns[$names[$c]][] = $value;
			}
		}
	}

	public function getColumn($name)
	{
		return $this->columns[$name];
	}

	public function getColumnNames()
	{
		return array_keys($this->columns);
	}

	public function getIterator()
    {
        return new \ArrayIterator($this->columns);
    }

    public function toArray()
    {
    	return $this->columns;
    }
}
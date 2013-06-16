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
            $rows = explode("\n", $data);
            foreach($rows as &$row) {
                $row = str_getcsv($row, self::CsvDelimiter);
                array_walk($row, function(&$value){
                    $value = trim($value);
                });
            }
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
        foreach ($cols as &$col) {
            if(!is_array($col)) {
                throw new \InvalidArgumentException("Each column needs to be an array");
            }
        }
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
            if(!is_array($rows[0])) {
                throw new \InvalidArgumentException("Row names need to be an array.");
            }
            $this->setColumnNames($rows[0]);
            $rows = array_slice($rows, 1);
        }
        $names = $this->getColumnNames();

        foreach ($rows as $r=>$row) {
            if (!is_array($row)) {
                throw new \InvalidArgumentException("Row needs to be an array.");
            }
            foreach ($row as $c=>$value) {
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

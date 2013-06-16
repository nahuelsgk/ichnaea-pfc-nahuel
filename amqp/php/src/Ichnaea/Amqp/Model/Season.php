<?php

namespace Ichnaea\Amqp\Model;

class Season implements \IteratorAggregate
{
    private $trials = array();
    const EndOfLine = PHP_EOL;

    public function __construct($data=null)
    {
        if ($data instanceof \SplFileInfo) {
            $data = $data->openFile("r");
        }
        if ($data instanceof \SplFileObject) {
            $contents = "";
            while (!$file->eof()) { 
                $file->next(); 
                $contents = $file->current().self::EndOfLine; 
            } 
            $data = $contents;
        }
        if (is_string($data)) {
            // remove comments
            $data = preg_replace("/^\s*#.*$\n?/m", "", $data);
            // split by trials
            $data = preg_split("/".self::EndOfLine."{2,}/", $data);
            foreach($data as &$trial) {
                $lines = explode(self::EndOfLine, trim($trial));
                $trial = array();
                foreach($lines as &$line) {
                    // split each key value line
                    $parts = preg_split("/\s+/", trim($line));
                    if(count($parts) != 2) {
                        throw new \InvalidArgumentException("Strange line '".$line."'.");
                    }
                    $trial[floatval($parts[0])] = floatval($parts[1]);
                }
            }
        }
        if (is_array($data)) {
            $this->setTrials($data);
        }
    }

    public function setTrials(array $trials)
    {
        foreach($trials as &$trial) {
            if(!is_array($trial)) {
                throw new \InvalidArgumentException("Each trial needs to be an array");
            }
        }
        $this->trials = $trials;
    }

    public function getTrials()
    {
        return $this->trials;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->trials);
    }

    public function toArray()
    {
        return $this->trials;
    }
}
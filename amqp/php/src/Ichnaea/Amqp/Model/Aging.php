<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a aging data. The aging
 * has a list of trials. Each trial has an array
 * of float key value pairs
 *
 * A aging can be loaded from and to a string in the form:
 * ```
 * # this is a trial
 * 0    4.00 
 * 48   3.85
 * 144  3.54
 * 288  3.08
 * 360  2.85
 * 
 * 
 * # this is another trial
 * 0    3.80
 * 48   3.66
 * 144  3.37
 * 288  2.94
 * 360  2.72
 * ```
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class Aging implements \IteratorAggregate
{
    /**
     * The trials data. Each element is a trial and
     * contains an array of float key value pairs
     *
     * @var array
     */    
    private $trials = array();

    /**
     * End of line string used to split lines
     *
     * @var string
     */
    const EndOfLine = PHP_EOL;

    /**
     * Constructor. The data parameter can be of multiple
     * types:
     *
     * * \SplFileInfo: loads from a aging file
     * * \SplFileObject: loads from a aging file
     * * string: loads from a aging string
     * * array loads from a trials array
     *
     * @param mixed the aging data
     */
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

    /**
     * Return if the aging is empty
     *
     * @return boolean true if the aging is empty
     */
    public function isEmpty()
    {
        foreach($this->trials as &$trial) {
            if(!empty($trial)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set the trials data. Each array element is
     * an array of float key value pairs.
     *
     * @param array $trials the trials data
     */
    public function setTrials(array $trials)
    {
        foreach($trials as &$trial) {
            if(!is_array($trial)) {
                throw new \InvalidArgumentException("Each trial needs to be an array");
            }
        }
        $this->trials = $trials;
    }

    /**
     * Get the trials data. Each array element is
     * an array of float key value pairs.
     *
     * @return array the trials data
     */
    public function getTrials()
    {
        return $this->trials;
    }

    /**
     * Return an array iterator to the parts
     * so that the aging can be used in fereach
     *
     * @see \IteratorAggregate
     * @return \ArrayIterator the iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->trials);
    }

    /**
     * Return the aging data as array
     *
     * @return array the data
     */
    public function toArray()
    {
        return $this->trials;
    }
}
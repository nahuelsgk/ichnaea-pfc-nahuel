<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents the agings associated
 * to a dataset. Each dataset column can have a list of
 * agings indexed by an unique position.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class DatasetAging implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $columns = array();

    public function __construct(array $data=array())
    {
        if(isset($data['files']) && is_array($data['files'])) {

        } else {
            $this->setColumns($data);
        }
    }

    /**
     * This adds a aging.
     * Each aging data is associated to a dataset column
     * And is set in a unitary position (typically Summer:0.5, Winter:1.0)
     * 
     * @param string  $column The dataset column associated to the aging
     * @param float   $position the unitary position of the aging
     * @param Aging  $aging The aging data     
     */
    public function addAging($column, $position, $aging)
    {
        if ($position < 0 || $position > 1) {
            throw new \InvalidArgumentException("Aging position should be unitary.");
        }
        if (!$aging instanceof Aging) {
            $aging = new Aging($aging);
        }
        if(!isset($this->columns[$column])) {
            $this->columns[$column] = array();
        }
        $this->columns[$column][(string)$position] = $aging;
    }

    /**
     * Load all the agings at once. The array should be indexed
     * by column name and then by aging position
     *
     * @param array $columns an array of arrays of agings
     */
    public function setColumns(array $columns)
    {
        foreach($columns as $col=>&$agings) {
            if(!is_array($agings)) {
                throw new \InvalidArgumentException("Each column should contain a aging array.");
            }
            foreach($agings as $pos=>&$aging) {
                $this->setAging($col, $pos, $aging);
            }
        }
    }

    /**
     * Return an array iterator to the parts
     * so that the dataset can be used in fereach
     *
     * @see \IteratorAggregate
     * @return \ArrayIterator the iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->columns);
    }

    /**
     * Return the dataset data as array
     *
     * @return array the data
     */
    public function toArray()
    {
        return $this->columns;
    }

    /**
     * Create the agings from an array
     *
     * @param array the agings data
     * @return DatasetAgings the agings
     */
    public static function fromArray(array $agings)
    {
        return new self($agings);
    }

    /**
     * Return if the agings are empty
     *
     * @return boolean true if the agings are empty
     */
    public function isEmpty()
    {
        foreach($this->columns as &$column) {
            if(!empty($column)) {
                return false;
            }
        }
        return true;
    }    
}

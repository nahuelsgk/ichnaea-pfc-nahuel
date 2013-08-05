<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents the seasons associated
 * to a dataset. Each dataset column can have a list of
 * seasons indexed by an unique position.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class DatasetSeasons implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $columns = array();

    public function __construct($columns=array())
    {
        if (is_array($columns)) {
            $this->setColumns($columns);
        }
    }

    /**
     * This adds a season.
     * Each season data is associated to a dataset column
     * And is set in a unitary position (typically Summer:0.5, Winter:1.0)
     * 
     * @param string  $column The dataset column associated to the season
     * @param float   $position the unitary position of the season
     * @param Season  $season The season data     
     */
    public function addSeason($column, $position, $season)
    {
        if ($position < 0 || $position > 1) {
            throw new \InvalidArgumentException("Season position should be unitary.");
        }
        if (!$season instanceof Season) {
            $season = new Season($season);
        }
        if(!isset($this->columns[$column])) {
            $this->columns[$column] = array();
        }
        $this->columns[$column][(string)$position] = $season;
    }

    /**
     * Load all the seasons at once. The array should be indexed
     * by column name and then by season position
     *
     * @param array $columns an array of arrays of seasons
     */
    public function setColumns(array $columns)
    {
        foreach($columns as $col=>&$seasons) {
            if(!is_array($seasons)) {
                throw new \InvalidArgumentException("Each column should contain a season array.");
            }
            foreach($seasons as $pos=>&$season) {
                $this->setSeason($col, $pos, $season);
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
     * Create the seasons from an array
     *
     * @param array the seasons data
     * @return DatasetSeasons the seasons
     */
    public static function fromArray(array $seasons)
    {
        return new self($seasons);
    }

    /**
     * Return if the seasons are empty
     *
     * @return boolean true if the seasons are empty
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

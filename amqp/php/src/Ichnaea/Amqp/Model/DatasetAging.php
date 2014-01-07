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
     * The data array can have two formats. The simple
     * One is an array of arrays indexed by column name
     * and then by unitary position. The complex one
     * is an associative array containing three keys:
     *
     * * `files` is an array of aging objects with the
     *   filenames as keys. The aging object can also
     *   be an array or something that can be loaded by
     *   the aging class
     * * `format` is a format string that can contain
     *   `%column%` and `%aging%` placeholders. This format
     *   is used to decide the column and the position of the
     *   aging
     * * `positions` is an array that relates `%aging%`
     *   placeholders to unitary position values
     *
     * @var array data for the dataset aging
     */
    private $columns = array();

    public function __construct(array $data=array())
    {
        if (isset($data['files']) && is_array($data['files'])) {
            $data = array_merge(array(
                'format'    => 'env%column%-%aging%.txt',
                'positions' => array()
            ), $data);
            $regex = '/^'.preg_replace('/%(.+?)%/', '(?<$1>.+?)', $data['format']).'$/';
            foreach ($data['files'] as $filename => $aging) {
                if ($data instanceof \SplFileInfo || $data instanceof \SplFileObject) {
                    $filename = $file->getFileInfo();
                }
                if (preg_match($regex, $filename, $m)) {
                    if (isset($data['positions'][$m['aging']])) {
                        $position = $data['positions'][$m['aging']];
                        $this->setAging($m['column'], $position, $aging);
                    }
                }
            }
        } else {
            $this->setColumns($data);
        }
    }

    /**
     * This sets a aging.
     * Each aging data is associated to a dataset column
     * And is set in a unitary position (typically Summer:0.5, Winter:1.0)
     *
     * @param string $column   The dataset column associated to the aging
     * @param float  $position the unitary position of the aging
     * @param Aging  $aging    The aging data
     */
    public function setAging($column, $position, $aging)
    {
        if ($position < 0 || $position > 1) {
            throw new \InvalidArgumentException("Aging position should be unitary.");
        }
        if (!$aging instanceof Aging) {
            $aging = new Aging($aging);
        }
        if (!isset($this->columns[$column])) {
            $this->columns[$column] = array();
        }
        $this->columns[$column][(string) $position] = $aging;
    }

    /**
     * Load all the agings at once. The array should be indexed
     * by column name and then by aging position
     *
     * @param array $columns an array of arrays of agings
     */
    public function setColumns(array $columns)
    {
        foreach ($columns as $col=>&$agings) {
            if (is_array($agings)) {
                foreach ($agings as $pos=>&$aging) {
                    $this->setAging($col, $pos, $aging);
                }
            }
        }
    }

    /**
     * Return an aging
     *
     * @param  string $name     the name of the column
     * @param  float  $position the position of the aging
     * @return Aging  the aging object or null
     */
    public function getAging($name, $position)
    {
        if(isset($this->columns[$name]) && is_array($this->columns[$name]) &&
            isset($this->columns[$name][(string) $position])) {
            return $this->columns[$name][(string) $position];
        } else {
            return null;
        }
    }

    /**
     * Return the data of a column by name
     *
     * @param  string $name the name of the column
     * @return array  the column data
     */
    public function getColumn($name)
    {
        return $this->columns[$name];
    }

    /**
     * Return the column names
     *
     * @return array the column names
     */
    public function getColumnNames()
    {
        return array_keys($this->columns);
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
        foreach ($this->columns as &$column) {
            if (!empty($column)) {
                return false;
            }
        }

        return true;
    }
}

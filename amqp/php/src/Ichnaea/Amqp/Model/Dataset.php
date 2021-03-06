<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a dataset. A dataset
 * contains a number of colums, each with a name
 * and a set of rows of values for each one
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class Dataset implements \IteratorAggregate
{
    /**
     * Column delimiter for reading and writing csv
     *
     * @var string
     */
    const CsvDelimiter = ";";

    /**
     * The columns data. Each element is a column and
     * Contains an array of values.
     *
     * @var array
     */
    private $columns = array();

    /**
     * Constructor. The data parameter can be of multiple
     * types:
     *
     * * \SplFileInfo: loads from a csv file
     * * \SplFileObject: loads from a csv file
     * * string: loads from a csv string
     * * array loads from a columns array
     *
     * @param mixed the dataset data
     */
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
            $rows = preg_split("/\r?\n/", $data);
            $rows = array_filter($rows, function ($row) {
                return !empty($row);
            });
            foreach ($rows as &$row) {
                $row = str_getcsv($row, self::CsvDelimiter);
                array_walk($row, function (&$value) {
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

    /**
     * Sets the columns
     *
     * @param array $cols columns
     */
    public function setColumns(array $cols)
    {
        foreach ($cols as &$col) {
            if (!is_array($col)) {
                throw new \InvalidArgumentException("Each column needs to be an array");
            }
        }
        $this->columns = $cols;
    }

    /**
     * Change the column names
     *
     * @param array $names the names of the columns
     */
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

    /**
     * Sets the data by rows. Each element of the array
     * is considered a row.
     *
     * @param array $rows      the row data
     * @param bool  $withNames if the first row contains the column names
     */
    public function setRows(array $rows, $withNames=false)
    {
        $rows = array_values($rows);
        if ($withNames) {
            if (!is_array($rows[0])) {
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
     * Return an array of rows
     *
     * @return array the rows
     */
    public function getRows()
    {
        $rows = array();
        $j = 0;
        foreach ($this->columns as $column) {
            foreach ($column as $i=>$cell) {
                if (!array_key_exists($i, $rows)) {
                    $rows[$i] = array();
                }
                while (count($rows[$i])<$j) {
                    $rows[$i][] = "";
                }
                $rows[$i][$j] = $cell;
            }
            $j++;
        }

        return $rows;
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
     * Return the dataset data as a string
     *
     * @param  string $sep the separatow between columns
     * @param  string $qt  the quote string
     * @param  string $nl  the newline between rows
     * @return string the string
     */
    public function toString($sep=", ", $qt='"', $nl="\n")
    {
        $str = "";
        $rows = $this->getRows();
        array_unshift($rows, $this->getColumnNames());
        foreach ($rows as $row) {
            foreach ($row as $k=>$cell) {
                $row[$k] = $qt.addslashes($cell).$qt;
            }
            $str .= join($sep, $row).$nl;
        }

        return trim($str);
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Return the dataset data as an html table
     * @return string the html
     **/
    public function toHtml()
    {
        $html = "<table>\n";
        $cols = $this->getColumnNames();
        $html .= "<thead><tr>";
        foreach ($cols as $col) {
            $html .= "<th>".$col."<th>";
        }
        $html .= "</tr></thead>\n";
        $rows = $this->getRows();
        $html .= "<tbody>\n";
        foreach ($rows as $row) {
            $html .= "<tr>";
            foreach ($row as $cel) {
                $html .= "<td>".$cel."<td>";
            }
            $html .= "</tr>\n";
        }
        $html .= "</tbody>\n";
        $html .= "</table>\n";

        return $html;
    }

    /**
     * Return if the dataset is empty
     *
     * @return boolean true if the dataset is empty
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

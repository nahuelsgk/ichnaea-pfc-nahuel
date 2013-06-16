<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a build models request
 *
 * This request contains:
 * * an unique identifier
 * * a dataset
 * * a list of seasons for each column
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class BuildModelsRequest
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $seasons = array();

    /**
     * @var Dataset
     */
    private $dataset;

    /**
     * Constructor for the build_models request
     *
     * @param string $id identifier for the request
     */
    public function __construct($id=null)
    {
        if (!$id) {
            $id = uniqid();
        }
        $this->id = $id;
        $this->dataset = new Dataset();
    }

    /**
     * Set the data for the build models columns
     * The data can be a Dataset object or
     * a dataset \SplFileObject, a string with csv data
     * or an array.
     *
     * @param mixed $dataset the data
     */
    public function setDataset($dataset)
    {
        if (!$dataset instanceof Dataset) {
            $dataset = new Dataset($dataset);
        }
        $this->dataset = $dataset;
    }

    /**
     * This adds season data to the build models request
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
        if(!isset($this->seasons[$column])) {
            $this->seasons[$column] = array();
        }
        $this->seasons[$column][(string)$position] = $season;
    }

    /**
     * Load all the seasons at once. The array should be indexed
     * by column name and then by season position
     *
     * @param array $seasons an array of Season objects
     */
    public function setSeasons(array $seasons)
    {
        foreach($seasons as $col=>&$colSeasons) {
            if(!is_array($colSeasons)) {
                throw new \InvalidArgumentException("Each column should contain a season array.");
            }
            foreach($colSeasons as $pos=>&$season) {
                $this->setSeason($col, $pos, $season);
            }
        }
    }

    /**
     * Get the request id
     *
     * @return string the request id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the entire seasons array
     *
     * @return array seasons
     */
    public function getSeasons()
    {
        return $this->seasons;
    }

    /**
     * Get the dataset
     * 
     * @return Dataset the dataset
     */
    public function getDataset()
    {
        return $this->dataset;
    }

    /**
     * Export the request to an array
     *
     * @return array the request data
     */
    public function toArray()
    {
        return array(
            "id"		=> $this->id,
            "seasons"	=> $this->seasons,
            "section"	=> $this->section,
            "dataset"	=> $this->dataset->toArray()
        );
    }

    /**
     * Update the request from an array
     *
     * @param array the request data
     */
    public function update(array $data)
    {
        if (array_key_exists('dataset', $data)) {
            $this->setDataset($data['dataset']);
        }
        if (array_key_exists('seasons', $data)) {
            $this->setSeasons($data['seasons']);
        }
        if (isset($data['fake_duration']) || isset($data['fake_interval'])) {
            $this->setFake($data['fake_duration'].":".$data['fake_interval']);
        }
    }

    /**
     * Create the request from an array
     *
     * @param array the request data
     * @return BuildModelsRequest the request
     */
    public static function fromArray(array $data)
    {
        $data = array_merge(array('id'=>null), $data);
        if(array_key_exists('fake', $data) && $data['fake']) {
            $req = new BuildModelsFakeRequest($data['id']);
        } else {
            $req = new self($data['id']);
        }
        $req->update($data);

        return $req;
    }

}

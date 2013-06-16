<?php

namespace Ichnaea\Amqp\Model;

class BuildModelsRequest
{
    private $id;
    private $seasons = array();
    private $dataset;
    private $fake;

    /**
     * Constructor for the build_models request
     * @param mixed $id itentifier for the request
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
     * Fake sets a fake request. The format should be
     * a string of type "T:I", where T is the total time
     * the fake request should take and I is the interval
     * in which the response will be updated
     */
    public function setFake($fake)
    {
        $this->fake = $fake;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSeasons()
    {
        return $this->seasons;
    }

    public function getDataset()
    {
        return $this->dataset;
    }

    public function getFake()
    {
        return $this->fake;
    }

    public function isFake()
    {
        return $this->fake != null;
    }

    public function toArray()
    {
        return array(
            "id"		=> $this->id,
            "seasons"	=> $this->seasons,
            "section"	=> $this->section,
            "fake"		=> $this->fake,
            "dataset"	=> $this->dataset->toArray()
        );
    }

    public function update(array $data)
    {
        if (array_key_exists('dataset', $data)) {
            $this->setDataset($data['dataset']);
        }
        if (array_key_exists('seasons', $data)) {
            $this->setSeasons($data['seasons']);
        }
        if (array_key_exists('fake', $data)) {
            $this->setFake($data['fake']);
        }
        if (isset($data['fake_duration']) || isset($data['fake_interval'])) {
            $this->setFake($data['fake_duration'].":".$data['fake_interval']);
        }
    }

    public static function fromArray(array $data)
    {
        $data = array_merge(array('id'=>null), $data);
        $req = new self($data['id']);
        $req->update($data);

        return $req;
    }

}

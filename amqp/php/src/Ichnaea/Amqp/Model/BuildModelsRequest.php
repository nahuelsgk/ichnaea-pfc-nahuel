<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a build models request
 *
 * This request contains:
 * * an unique identifier
 * * a dataset
 * * a list of agings for each column
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
     * @var DatasetAging
     */
    private $aging;

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
        $this->aging = new DatasetAging();
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
     * Set the data for the aging
     *
     * @param mixed $aging the data
     */
    public function setAging($aging)
    {
        if (!$aging instanceof DatasetAging) {
            $aging = new DatasetAging($aging);
        }
        $this->aging = $aging;
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
     * Get the entire aging data
     *
     * @return DatasetAging aging
     */
    public function getAging()
    {
        return $this->aging;
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
            "aging"	    => $this->aging->toArray(),
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
        if (array_key_exists('aging', $data)) {
            $this->setAging($data['aging']);
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

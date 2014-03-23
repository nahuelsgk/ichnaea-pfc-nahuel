<?php

namespace Ichnaea\Amqp\Model;

/**
 * This class represents a predict models request
 *
 * This request contains:
 * * an unique identifier
 * * the data returned from a predict models response
 * * a test dataset
 *
 * @see PredictModelsResponse
 * @author Miguel Ibero <miguel@ibero.me>
 */
class PredictModelsRequest
{
    /**
     * @var string
     */
    private $id;

    /**
     * The data returned in a previous BuildModelsRequest
     *
     * @var mixed
     */
    private $data = null;

    /**
     * The test dataset
     *
     * @var Dataset
     */
    private $dataset;

    /**
     * Constructor for the predict request
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
     * Sets the data
     *
     * @param mixed $data the data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Gets the data
     *
     * @return mixed the data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data for the predict models columns
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
     * Get the dataset
     *
     * @return Dataset the dataset
     */
    public function getDataset()
    {
        return $this->dataset;
    }

    /**
     * Gets the id
     *
     * @return string the id
     */
    public function getId()
    {
        return $this->id;
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
            "data"	    => $this->data,
            "dataset"   => $this->dataset->toArray()
        );
    }

    /**
     * Update the request from an array
     *
     * @param array the request data
     */
    public function update(array $data)
    {
        if (array_key_exists('data', $data)) {
            $this->setData($data['data']);
        }
        if (array_key_exists('dataset', $data)) {
            $this->setDataset($data['dataset']);
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
        $req = new self($data['id']);
        $req->update($data);

        return $req;
    }

}

<?php
namespace Ichnaea\WebApp\PredictionBundle\Entity;

use JMS\SecurityExtraBundle\Security\Util\String;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prediction Column
 * 
 * @ORM\Table(name="prediction_column")
 * @ORM\Entity
 */
class PredictionColumn
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @var String
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="position", type="integer", nullable=false)
	 */
	private $index;
	
	/**
	 * @ORM\ManyToOne(targetEntity="PredictionMatrix", inversedBy="columns")
	 */
	private $prediction;
	
	/**
    * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig")
  	* @ORM\JoinColumn(name="column_configuration_id", referencedColumnName="id", nullable=true)  
    */
	private $columnConfiguration;
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Set name
	 *
	 * @param string $name
	 * @return PredictionColumn
	 */
	public function setName($name)
	{
		$this->name = $name;
	
		return $this;
	}
	
	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Set index
	 *
	 * @param int $index
	 * @return PredictionColumn
	 */
	public function setIndex($index)
	{
		$this->index = $index;
	
		return $this;
	}
	
	/**
	 * Get index
	 *
	 * @return integer
	 */
	public function getIndex()
	{
		return $this->index;
	}
	
	
	/**
	 * Set matrix
	 *
	 * @param \Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix $matrix
	 * @return PredictionColumn
	 */
	public function setPrediction(\Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix $prediction = null)
	{
		$this->prediction = $prediction;
	
		return $this;
	}
	
	/**
	 * Get matrix
	 *
	 * @return \Ichnaea\WebApp\Bundle\Entity\PredictionMatrix
	 */
	public function getPrediction()
	{
		return $this->prediction;
	}
    /**
     * Constructor
     */
    public function __construct()
    {
    }


    /**
     * Set columnConfiguration
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columnConfiguration
     * @return PredictionColumn
     */
    public function setColumnConfiguration(\Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columnConfiguration = null)
    {
        $this->columnConfiguration = $columnConfiguration;

        return $this;
    }

    /**
     * Get columnConfiguration
     *
     * @return \Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig 
     */
    public function getColumnConfiguration()
    {
        return $this->columnConfiguration;
    }
}

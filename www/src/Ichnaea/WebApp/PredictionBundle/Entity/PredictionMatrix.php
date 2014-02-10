<?php 
namespace Ichnaea\WebApp\PredictionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Matrix
 *
 * @ORM\Table(name="prediction_matrix")
 * @ORM\Entity
 */

class PredictionMatrix
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
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\TrainingBundle\Entity\Training")
	 * @ORM\JoinColumn(name="training_id", referencedColumnName="id")
	 */
	private $training;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\UserBundle\Entity\User", inversedBy="matrixs")
	 */
	private $owner;
	
	/**
	 * @ORM\OneToMany(targetEntity="Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample", mappedBy="matrix", cascade={"persist"})
	 */
	private $rows;
	
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
     * Constructor
     */
    public function __construct()
    {
        $this->rows = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PredictionMatrix
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
     * Set training
     *
     * @param \Ichnaea\WebApp\TrainingBundle\Entity\Training $training
     * @return PredictionMatrix
     */
    public function setTraining(\Ichnaea\WebApp\TrainingBundle\Entity\Training $training = null)
    {
        $this->training = $training;

        return $this;
    }

    /**
     * Get training
     *
     * @return \Ichnaea\WebApp\TrainingBundle\Entity\Training 
     */
    public function getTraining()
    {
        return $this->training;
    }

    /**
     * Add rows
     *
     * @param \Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample $rows
     * @return PredictionMatrix
     */
    public function addRow(\Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample $rows)
    {
        $this->rows[] = $rows;

        return $this;
    }

    /**
     * Remove rows
     *
     * @param \Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample $rows
     */
    public function removeRow(\Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample $rows)
    {
        $this->rows->removeElement($rows);
    }

    /**
     * Get rows
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Set owner
     *
     * @param \Ichnaea\WebApp\UserBundle\Entity\User $owner
     * @return PredictionMatrix
     */
    public function setOwner(\Ichnaea\WebApp\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Ichnaea\WebApp\UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}

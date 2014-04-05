<?php 
namespace Ichnaea\WebApp\PredictionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Matrix
 *
 * @ORM\Table(name="prediction_matrix")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
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
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255, nullable=true)
	 */
	private $description;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\TrainingBundle\Entity\Training")
	 * @ORM\JoinColumn(name="training_id", referencedColumnName="id")
	 */
	private $training;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\UserBundle\Entity\User", inversedBy="predictions")
	 */
	private $owner;
	
	/**
	 * @ORM\OneToMany(targetEntity="Ichnaea\WebApp\PredictionBundle\Entity\PredictionSample", mappedBy="matrix", cascade={"persist"})
	 */
	private $rows;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created", type="datetime", nullable=false)
	 */
	private $created;
	
	/**
	 * @var string
	 * @ORM\Column(name="status", type="string", columnDefinition="ENUM('new', 'pending', 'sent', 'finished')")
	 */
	private $status = 'new';
	
	/**
	 * @var string
	 * @ORM\Column(name="error", type="string", length=255, nullable = true	)
	 */
	private $error;
	
	/**
	 * @var string
	 * @ORM\Column(name="request_id", type="string", length=255, nullable = true)
	 */
	private $requestId;
	
	/**
	 * @var decimal
	 * @ORM\Column(name="progress", type="decimal", precision=5, scale=2)
	 */
	private $progress = 0;
	
	/**
     * @var array
     *
     * @ORM\Column(name="results", type="array", nullable = true)
     */
    private $predictions_result;
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
     * Set description
     *
     * @param string $description
     * @return Training
     */
    public function setDescription($description)
    {
    	$this->description = $description;
    
    	return $this;
    }
    
    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
    	return $this->description;
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

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Training
     */
    public function setCreated($created)
    {
    	$this->created = $created;
    
    	return $this;
    }
    
    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
    	return $this->created;
    }
    
    /**
     * Set status
     *
     * @param string $status
     * @return PredictionMatrix
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function setStatusAsPending()
    {
    	$this->setStatus("pending");
    }
    public function setStatusAsSent()
    {
    	$this->setStatus("sent");
    }
    public function setStatusAsFinished()
    {
    	$this->setStatus("finished");
    }
    
    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set error
     *
     * @param string $error
     * @return PredictionMatrix
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return string 
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set requestId
     *
     * @param string $requestId
     * @return PredictionMatrix
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Get requestId
     *
     * @return string 
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set predictions_result
     *
     * @param array $predictionsResult
     * @return PredictionMatrix
     */
    public function setPredictionsResult($predictionsResult)
    {
        $this->predictions_result = $predictionsResult;

        return $this;
    }

    /**
     * Get predictions_result
     *
     * @return array 
     */
    public function getPredictionsResult()
    {
        return $this->predictions_result;
    }

    /**
     * Set progress
     *
     * @param string $progress
     * @return PredictionMatrix
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return string 
     */
    public function getProgress()
    {
        return $this->progress;
    }
    
    /**
     *  @ORM\PrePersist
     */
    public function createdDefault()
    {
    	$date = new \DateTime("now");
    	$this->created = $date;
    }
    
}

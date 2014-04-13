<?php
namespace Ichnaea\WebApp\TrainingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Training
 *
 * @ORM\Table(name="training")
 * @ORM\Entity 
 * @ORM\HasLifecycleCallbacks 
 */
class Training
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
     * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\UserBundle\Entity\User", inversedBy="trainings") 
     */
    protected $trainer;

    /**
     * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\MatrixBundle\Entity\Matrix", inversedBy="training")
     * @ORM\JoinColumn(name="matrix_id", referencedColumnName="id", nullable=false)
     */
    protected $matrix;
    
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
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;
        
    /**
     * @var string
     * @ORM\Column(name="status", type="string", columnDefinition="ENUM('pending', 'sent', 'finished')") 
     */
    private $status = 'pending';    
    
    /**
     * @var string
     * @ORM\Column(name="request_id", type="string", length=255, nullable = false) 
     */
    private $requestId;
    
    /**
     * @var decimal
     * @ORM\Column(name="progress", type="decimal", precision=5, scale=2)
     */
    private $progress = 0;
    
    /**
     * @var string
     * @ORM\Column(name="error", type="string", length=255, nullable = true	)
     */
    private $error;

    /**
     * @var string
     * @ORM\Column(name="origin_versus", type="string", length=255, nullable = true	)
     */
    private $originVersus;

    
    /**
    * @ORM\ManyToMany(targetEntity="Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig")
    * @ORM\JoinTable(name="training_variable_matrix_config",
    *  joinColumns={@ORM\JoinColumn(name="training_id", referencedColumnName="id")},
    *  inverseJoinColumns={@ORM\JoinColumn(name="column_id", referencedColumnName="id")}
    * )  
    */
	private $columnsSelected;
	
	
    /** Get id
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
     * @return Training
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
     * Set lastModification
     *
     * @param \DateTime $lastModification
     * @return Training
     */
    public function setLastModification($lastModification)
    {
        $this->lastModification = $lastModification;

        return $this;
    }

    /**
     * Get lastModification
     *
     * @return \DateTime 
     */
    public function getLastModification()
    {
        return $this->lastModification;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Training
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
    *  @ORM\PrePersist 
    */
    public function createdDefault()
    { 
    	$date = new \DateTime("now");
    	$this->created = $date;	 
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->trainer = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add trainer
     *
     * @param \Ichnaea\WebApp\UserBundle\Entity\User $trainer
     * @return Training
     */
    public function addTrainer(\Ichnaea\WebApp\UserBundle\Entity\User $trainer)
    {
        $this->trainer[] = $trainer;

        return $this;
    }

    /**
     * Remove trainer
     *
     * @param \Ichnaea\WebApp\UserBundle\Entity\User $trainer
     */
    public function removeTrainer(\Ichnaea\WebApp\UserBundle\Entity\User $trainer)
    {
        $this->trainer->removeElement($trainer);
    }

    /**
     * Get trainer
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrainer()
    {
        return $this->trainer;
    }

    /**
     * Set trainer
     *
     * @param \Ichnaea\WebApp\UserBundle\Entity\User $trainer
     * @return Training
     */
    public function setTrainer(\Ichnaea\WebApp\UserBundle\Entity\User $trainer = null)
    {
        $this->trainer = $trainer;

        return $this;
    }

    /**
     * Set matrix
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Matrix $matrix
     * @return Training
     */
    public function setMatrix(\Ichnaea\WebApp\MatrixBundle\Entity\Matrix $matrix = null)
    {
        $this->matrix = $matrix;

        return $this;
    }

    /**
     * Get matrix
     *
     * @return \Ichnaea\WebApp\MatrixBundle\Entity\Matrix 
     */
    public function getMatrix()
    {
        return $this->matrix;
    }

    /**
     * Set error
     *
     * @param string $error
     * @return Training
     */
    public function setRequestId($id)
    {
    	$this->requestId = $id;
    	
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
     * Set error
     *
     * @param string $error
     * @return Training
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
     * Set origin
     *
     * @param string $origin
     * @return Training
     */
    public function setOrigin($origin)
    {
    	$this->originVersus = $origin;
    	return $this;
    }
    
    /**
     * Get origin
     *
     * @return string
     */
    public function getOriginVersus()
    {
    	return $this->originVersus;
    }
    
    /**
     * Set progress
     * 
     * @param decimal $progress
     * @return Training
     */
    public function setProgress	($progress) {
    	$this->progress = $progress;
    	return $this;
    }
    
    /**
     * Get progress
     * 
     * @return decimal
     */
    public function getProgress() {
    	return $this->progress;
    }
    

    /**
     * Add columnsSelected
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columnsSelected
     * @return Training
     */
    public function addColumnsSelected(\Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columnsSelected)
    {
        $this->columnsSelected[] = $columnsSelected;

        return $this;
    }

    /**
     * Remove columnsSelected
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columnsSelected
     */
    public function removeColumnsSelected(\Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columnsSelected)
    {
        $this->columnsSelected->removeElement($columnsSelected);
    }

    /**
     * Get columnsSelected
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getColumnsSelected()
    {
        return $this->columnsSelected;
    }
}

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
     * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\UserBundle\Entity\User", inversedBy="trainers") 
     */
    protected $trainer;

    /**
     * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\MatrixBundle\Entity\Matrix", inversedBy="trainings")
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
     * @var integer
     *
     * @ORM\Column(name="k1", type="integer", nullable=false)
     */
    private $k1 = 10;

    /**
     * @var integer
     *
     * @ORM\Column(name="k2", type="integer", nullable=false)
     */
    private $k2 = 10;
	
    /**
     * @var float
     *
     * @ORM\Column(name="best_models", type="decimal", nullable=false, scale=5, precision=10)
     */
    private $bestModels = 0.25;

    /**
     * @var integer
     *
     * @ORM\Column(name="min_size_variable_set", type="integer", nullable=false)
     */
    private $minSizeVariableSet = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_size_variable_set", type="integer", nullable=false)
     */
    private $maxSizeVariableSet = 5;
    
    /**
     * @var string
     * @ORM\Column(name="type_of_search", type="string", columnDefinition="ENUM('fordward', 'backward', 'exhaustive')", nullable=false)
     */
    private $typeOfSearch = "backward";
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="pathHeatMap", type="string", length=255, nullable = true)
     */
    private $pathHeatMap;

    /**
     * @var string
     *
     * @ORM\Column(name="pathTable", type="string", length=255, nullable = true)
     */
    private $pathTable;

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
     * Set k1
     *
     * @param integer $k1
     * @return Training
     */
    public function setK1($k1)
    {
        $this->k1 = $k1;

        return $this;
    }

    /**
     * Get k1
     *
     * @return integer 
     */
    public function getK1()
    {
        return $this->k1;
    }

    /**
     * Set k2
     *
     * @param integer $k2
     * @return Training
     */
    public function setK2($k2)
    {
        $this->k2 = $k2;

        return $this;
    }

    /**
     * Get k2
     *
     * @return integer 
     */
    public function getK2()
    {
        return $this->k2;
    }

    /**
     * Set bestModels
     *
     * @param float $bestModels
     * @return Training
     */
    public function setBestModels($bestModels)
    {
        $this->bestModels = $bestModels;

        return $this;
    }

    /**
     * Get bestModels
     *
     * @return float 
     */
    public function getBestModels()
    {
        return $this->bestModels;
    }

    /**
     * Set minSizeVariableSet
     *
     * @param integer $minSizeVariableSet
     * @return Training
     */
    public function setMinSizeVariableSet($minSizeVariableSet)
    {
        $this->minSizeVariableSet = $minSizeVariableSet;

        return $this;
    }

    /**
     * Get minSizeVariableSet
     *
     * @return integer 
     */
    public function getMinSizeVariableSet()
    {
        return $this->minSizeVariableSet;
    }

    /**
     * Set maxSizeVariableSet
     *
     * @param integer $maxSizeVariableSet
     * @return Training
     */
    public function setMaxSizeVariableSet($maxSizeVariableSet)
    {
        $this->maxSizeVariableSet = $maxSizeVariableSet;

        return $this;
    }

    /**
     * Get maxSizeVariableSet
     *
     * @return integer 
     */
    public function getMaxSizeVariableSet()
    {
        return $this->maxSizeVariableSet;
    }

    /**
     * Set pathHeatMap
     *
     * @param string $pathHeatMap
     * @return Training
     */
    public function setPathHeatMap($pathHeatMap)
    {
        $this->pathHeatMap = $pathHeatMap;

        return $this;
    }

    /**
     * Get pathHeatMap
     *
     * @return string 
     */
    public function getPathHeatMap()
    {
        return $this->pathHeatMap;
    }

    /**
     * Set pathTable
     *
     * @param string $pathTable
     * @return Training
     */
    public function setPathTable($pathTable)
    {
        $this->pathTable = $pathTable;

        return $this;
    }

    /**
     * Get pathTable
     *
     * @return string 
     */
    public function getPathTable()
    {
        return $this->pathTable;
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
     * Set typeOfSearch
     *
     * @param string $typeOfSearch
     * @return Training
     */
    public function setTypeOfSearch($typeOfSearch)
    {
        $this->typeOfSearch = $typeOfSearch;

        return $this;
    }

    /**
     * Get typeOfSearch
     *
     * @return string 
     */
    public function getTypeOfSearch()
    {
        return $this->typeOfSearch;
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
    
}

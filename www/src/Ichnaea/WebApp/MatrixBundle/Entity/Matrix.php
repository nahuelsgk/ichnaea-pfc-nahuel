<?php
namespace Ichnaea\WebApp\MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Matrix
 *
 * @ORM\Table(name="matrix")
 * @ORM\Entity
 */
class Matrix
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
     * @ORM\OneToMany(targetEntity="VariableMatrixConfig", mappedBy="matrix", cascade={"persist"})
     */
    private $columns;
    
    /**
     * @ORM\OneToMany(targetEntity="Sample", mappedBy="matrix", cascade={"persist"})
     */
    private $rows;
    
    /**
     * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\UserBundle\Entity\User", inversedBy="matrixs")
     */								
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="Ichnaea\WebApp\TrainingBundle\Entity\Training", mappedBy="matrix")
     */
    private $training;
    
	/**
	 * @var boolean  
	 * 
	 * @ORM\Column(name="visible", type="boolean", nullable=false, options={"default" = 0})
	 * 
	 */    
    private $visible = false;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->columns = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set id
     *
     * @return integer
     */
    public function setId($id)
    {
    	$this->id = $id;
    	
    	return $this;
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Matrix
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
     * Add columns
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columns
     * @return Matrix
     */
    public function addColumn(\Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columns)
    {
        $this->columns[] = $columns;

        return $this;
    }

    /**
     * Remove columns
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columns
     */
    public function removeColumn(\Ichnaea\WebApp\MatrixBundle\Entity\VariableMatrixConfig $columns)
    {
        $this->columns->removeElement($columns);
    }

    /**
     * Get columns
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Add rows
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Sample $rows
     * @return Matrix
     */
    public function addRow(\Ichnaea\WebApp\MatrixBundle\Entity\Sample $rows)
    {
        $this->rows[] = $rows;

        return $this;
    }

    /**
     * Remove rows
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Sample $rows
     */
    public function removeRow(\Ichnaea\WebApp\MatrixBundle\Entity\Sample $rows)
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
     * @return Matrix
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
     * Set visible
     *
     * @param boolean $visible
     * @return Matrix
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }
    
    /**
     * Checks if the matrix is trainable. The matrix is trainible if is visible
     */
    public function isTrainable() {
    	if ($this->getVisible() == FALSE || $this->isComplete() == FALSE) return false;
    	return true;
    }
    
    
    public function isComplete() {
    	foreach ($this->getRows() as $row)
    	{
    		if ($row->getOrigin() == null) return false;
    	}
    	return true;
    }
    /**
     * Check if the matrix is still update-able. Basically check if have any training
     */
    public function isUpdatable() 
    { 
    	if ($this->getTraining()->count() > 0) return FALSE;
    	return true;  
    }
    
    public function isTrained()
    {
    	if ($this->getTraining()->count() > 0) return FALSE;
    	return true;
    }

    /**
     * Add training
     *
     * @param \Ichnaea\WebApp\TrainingBundle\Entity\Training $training
     * @return Matrix
     */
    public function addTraining(\Ichnaea\WebApp\TrainingBundle\Entity\Training $training)
    {
        $this->training[] = $training;

        return $this;
    }

    /**
     * Remove training
     *
     * @param \Ichnaea\WebApp\TrainingBundle\Entity\Training $training
     */
    public function removeTraining(\Ichnaea\WebApp\TrainingBundle\Entity\Training $training)
    {
        $this->training->removeElement($training);
    }

    /**
     * Get training
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTraining()
    {
        return $this->training;
    }
    
    /**
     * Get an associate array with all the origins
     */
    public function getOrigins(){
    	$originsArray = array();
    	foreach($this->getRows() as $sample){
    		$originsArray[$sample->getOrigin()] = $sample->getOrigin(); 
    	}
    	return $originsArray;
    }
    
    public function validate(){
    	
    }
    public function __clone(){
    }
}

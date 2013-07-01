<?php

namespace Ichnaea\WebApp\MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
}

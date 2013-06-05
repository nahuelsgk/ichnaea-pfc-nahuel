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
     * @var string
     *
     * @ORM\Column(name="matrix_id", type="string", length=255)
     */
    private $matrixId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetimetz")
     */
    private $created;


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
     * Set matrixId
     *
     * @param string $matrixId
     * @return Matrix
     */
    public function setMatrixId($matrixId)
    {
        $this->matrixId = $matrixId;
    
        return $this;
    }

    /**
     * Get matrixId
     *
     * @return string 
     */
    public function getMatrixId()
    {
        return $this->matrixId;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Matrix
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
}

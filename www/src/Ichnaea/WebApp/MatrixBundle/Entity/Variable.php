<?php

namespace Ichnaea\WebApp\MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet;

/**
 * Variable
 *
 * @ORM\Table(name="variable")
 * @ORM\Entity
 */
class Variable
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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="SeasonSet", mappedBy="variable") 
     * 
     */
    private $seasonSet;

	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->seasonSet = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Variable
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
     * @return Variable
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
     * Add seasonSet
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet $seasonSet
     * @return Variable
     */
    public function addSeasonSet(\Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet $seasonSet)
    {
        $this->seasonSet[] = $seasonSet;

        return $this;
    }

    /**
     * Remove seasonSet
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet $seasonSet
     */
    public function removeSeasonSet(\Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet $seasonSet)
    {
        $this->seasonSet->removeElement($seasonSet);
    }

    /**
     * Get seasonSet
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSeasonSet()
    {
        return $this->seasonSet;
    }
}

<?php

namespace Ichnaea\WebApp\MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SeasonSet
 *
 * @ORM\Table(name="season_set")
 * @ORM\Entity
 */
class SeasonSet
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
     * @ORM\ManyToOne(targetEntity="Variable", inversedBy="seasonSet") 
     */
    private $variable;
    
    /**
     * 
     * @ORM\ManyToMany(targetEntity="Season")
     * @ORM\JoinTable(name="season_set_comp",
     * 		joinColumns={@ORM\JoinColumn(name="season_id", referencedColumnName="id")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="season_set_id", referencedColumnName="id")}
     * )
     * 
     */
    protected $season;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->season = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return SeasonSet
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
     * Add season
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Season $season
     * @return SeasonSet
     */
    public function addSeason(\Ichnaea\WebApp\MatrixBundle\Entity\Season $season)
    {
        $this->season[] = $season;
        return $this;
    }

    /**
     * Remove season
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Season $season
     */
    public function removeSeason(\Ichnaea\WebApp\MatrixBundle\Entity\Season $season)
    {
        $this->season->removeElement($season);
    }

    /**
     * Get season
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set variable
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Variable $variable
     * @return SeasonSet
     */
    public function setVariable(\Ichnaea\WebApp\MatrixBundle\Entity\Variable $variable = null)
    {
        $this->variable = $variable;

        return $this;
    }

    /**
     * Get variable
     *
     * @return \Ichnaea\WebApp\MatrixBundle\Entity\Variable 
     */
    public function getVariable()
    {
        return $this->variable;
    }
}

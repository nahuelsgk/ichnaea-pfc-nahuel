<?php 
namespace Ichnaea\WebApp\MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SeasonSetComponent
 *
 * @ORM\Entity
 * @ORM\Table(name="season_set_comp")
 */
class SeasonSetComponent
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
 * 
 * @ORM\ManyToOne(targetEntity="SeasonSet")
 * @ORM\JoinColumn(name="season_set_id", referencedColumnName="id")
 * 
 */
private $seasonSet;

/**
 * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\MatrixBundle\Entity\Season")
 * @ORM\JoinColumn(name="season_id", referencedColumnName="id")
 *
 */
private $season;

/**
* @var string
* 
* @ORM\Column(name="type_comp", type="string", columnDefinition="ENUM('all_year', 'summer', 'winter', 'spring', 'autumn')") 
*/
private $seasonType = 'all_year';
    
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
     * Set season_type
     *
     * @param string $seasonType
     * @return SeasonSetComponent
     */
    public function setSeasonType($seasonType)
    {
        $this->seasonType = $seasonType;

        return $this;
    }

    /**
     * Get season_type
     *
     * @return string 
     */
    public function getSeasonType()
    {
        return $this->seasonType;
    }

    /**
     * Add seasonSet
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet $seasonSet
     * @return SeasonSetComponent
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

    /**
     * Set season
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Season $season
     * @return SeasonSetComponent
     */
    public function setSeason(\Ichnaea\WebApp\MatrixBundle\Entity\Season $season = null)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return \Ichnaea\WebApp\MatrixBundle\Entity\Season 
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set seasonSet
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet $seasonSet
     * @return SeasonSetComponent
     */
    public function setSeasonSet(\Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet $seasonSet = null)
    {
        $this->seasonSet = $seasonSet;

        return $this;
    }
}

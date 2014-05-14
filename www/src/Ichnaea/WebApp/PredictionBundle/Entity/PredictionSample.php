<?php

namespace Ichnaea\WebApp\PredictionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PredictionSample
 *
 * @ORM\Table(name="prediction_sample")
 * @ORM\Entity
 */
class PredictionSample
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
     * @ORM\Column(name="origin", type="string", length=255, nullable=true)
     */
    private $origin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var array
     *
     * @ORM\Column(name="samples", type="array")
     */
    private $samples;

    /**
     * @ORM\ManyToOne(targetEntity="PredictionMatrix", inversedBy="rows")
     */
	private $matrix;
	
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
     * @return Sample
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
     * Set origin
     *
     * @param string $origin
     * @return Sample
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string 
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Sample
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set samples
     *
     * @param array $samples
     * @return Sample
     */
    public function setSamples($samples)
    {
        $this->samples = $samples;

        return $this;
    }

    /**
     * Get samples
     *
     * @return array 
     */
    public function getSamples()
    {
        return $this->samples;
    }

    /**
     * Set matrix
     *
     * @param \Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix $matrix
     * @return PredictionSample
     */
    public function setMatrix(\Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix $matrix = null)
    {
        $this->matrix = $matrix;

        return $this;
    }

    /**
     * Get matrix
     *
     * @return \Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix 
     */
    public function getMatrix()
    {
        return $this->matrix;
    }
}

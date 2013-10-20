<?php
namespace Ichnaea\WebApp\MatrixBundle\Entity;

use JMS\SecurityExtraBundle\Security\Util\String;

use Doctrine\ORM\Mapping as ORM;

/**
 * VariableMatrixConfig: This a column of a matrix
 *
 * @ORM\Table(name="variable_matrix_config")
 * @ORM\Entity
 */
class VariableMatrixConfig
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
     * @var String
     * 
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\ManyToOne(targetEntity="Variable")
     * @ORM\JoinColumn(name="variable_id", referencedColumnName="id")
     */
    private $variable;
    
    /**
     * @ORM\ManyToOne(targetEntity="SeasonSet")
     * @ORM\JoinColumn(name="seasonSet", referencedColumnName="id")
     */

    private $seasonSet;
    
    /**
     * @ORM\ManyToOne(targetEntity="Matrix", inversedBy="columns")
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
     * @return VariableMatrixConfig
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
     * Set variable
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Variable $variable
     * @return VariableMatrixConfig
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

    /**
     * Set seasonSet
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet $seasonSet
     * @return VariableMatrixConfig
     */
    public function setSeasonSet(\Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet $seasonSet = null)
    {
        $this->seasonSet = $seasonSet;

        return $this;
    }

    /**
     * Get seasonSet
     *
     * @return \Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet 
     */
    public function getSeasonSet()
    {
        return $this->seasonSet;
    }

    /**
     * Set matrix
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Matrix $matrix
     * @return VariableMatrixConfig
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
}
